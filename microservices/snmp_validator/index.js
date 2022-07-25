require('dotenv').config()

const express = require("express");
const app = express();
const bodyParser = require("body-parser");
const Influx = require("influx");
const snmp = require("net-snmp");

const influx = new Influx.InfluxDB({
  host: process.env.INFLUXDB_HOST,
  database: process.env.INFLUXDB_DATABASE,
  schema: [
    {
      measurement: 'snmp_test',
      fields: { value: Influx.FieldType.INTEGER },
      tags: ['uid']
    }
  ]
});

influx.getDatabaseNames().then(names => {
  if (!names.includes(process.env.INFLUXDB_DATABASE)) {
    return influx.createDatabase(process.env.INFLUXDB_DATABASE);
  }
});

app.use(bodyParser.json());

const makeSnpmRequest = (session, OID) => {
  return new Promise((resolve, reject) => {
    session.get(OID, function (error, varbinds) {
      if (error) {
        console.error(error);
        reject(error);
      } else {
        for (let i = 0; i < varbinds.length; i++)
          if (snmp.isVarbindError(varbinds[i]))
            reject(snmp.varbindError(varbinds[i]));
          else {
            resolve(varbinds[i].value); // Only return the first one for now
          }
      }
    });
  });
}


app.post("/", async (req, res) => {
  let response = {
    value: 0
  };
  const HOST = req.body.target;
  const OID = [req.body.oid];
  const COMMUNITY = req.body.community;
  const DATA_TYPE = req.body.data_type;
  const uid = req.body.uid;
  let port = req.body.port;
  let ipv6 = req.body.ipv6;
  console.log(req.body)
  var verification1;
  var verification2;
  var time1;
  var time2;

  const session = snmp.createSession(HOST, COMMUNITY, options);
  try {

    var options = {
      port: 161,
      retries: 1,
      timeout: 5000,
      backoff: 1.0,
      transport: "udp4",
      trapPort: 162,
      version: snmp.Version1,
      backwardsGetNexts: true,
      idBitsSize: 32
    };
    if (port) options.port = port;

    /*
        var user = {
          name: COMMUNITY,
          level: snmp.SecurityLevel.noAuthNoPriv
        };
    */

    // if (DATA_TYPE != 'bandwidth_in' && DATA_TYPE != 'bandwidth_out') {

    response.value = await makeSnpmRequest(session, OID);

    // } else {

    // session.get(oidNetIn, function (error, varbinds) {
    //   if (error) {
    //     console.error(error);
    //   } else {
    //     for (let i = 0; i < varbinds.length; i++)
    //       if (snmp.isVarbindError(varbinds[i]))
    //         console.error(snmp.varbindError(varbinds[i]));
    //       else {
    //         let timestamp = ((new Date()).getTime()) / 1000; //parse to seconds
    //         verification1 = varbinds[i].value;
    //         time1 = timestamp;
    //       }
    //   }
    // })
    //   setTimeout(function () {
    //     session.get(oidNetIn, function (error, varbinds) {
    //       if (error) {
    //         console.error(error);
    //       } else {

    //         for (let i = 0; i < varbinds.length; i++)
    //           if (snmp.isVarbindError(varbinds[i]))
    //             console.error(snmp.varbindError(varbinds[i]));
    //           else {

    //             let timestamp = ((new Date()).getTime()) / 1000; //parse to seconds
    //             verification2 = varbinds[i].value;
    //             time2 = timestamp;

    //             var result = ((verification2 - verification1) / (time2 - time1)) * 8;
    //             response.value = result;
    //           }
    //       }

    //     })
    //   }, 30000);
    // }
  } catch (e) {
    console.error("error in snmp test", e);
    response = e.response;
  } finally {
    console.log('La response', response);
    await influx.writePoints([
      {
        measurement: 'snmp_test',
        tags: {
          uid: uid,
        },
        fields: {
          value: response.value,
          //duration: (response.duration / 1000).toFixed(2)
        }
      }
    ]);
    session.close();
  }
  res.send("OK");
});

app.listen(process.env.PORT);
