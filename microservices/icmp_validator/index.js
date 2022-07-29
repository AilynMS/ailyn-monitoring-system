require('dotenv').config()

const express = require("express");
const ping = require("ping");
const app = express();
const bodyParser = require("body-parser");
const Influx = require("influx");

function systemPing (host) {
  try {
    var exec = require('child_process').exec;
    const makePingCall = (error, stdout, stderr) => console.log(error, stdout, stderr)

    exec(`ping ${host} -n 1`, makePingCall);
  } catch (error) {
    console.error(error)
  }
}
console.log(systemPing('google.com'))
const influx = new Influx.InfluxDB({
  host: process.env.INFLUXDB_HOST,
  database: process.env.INFLUXDB_DATABASE,
  schema: [
    {
      measurement: "icmp_test",
      fields: {
        is_alive: Influx.FieldType.BOOLEAN,
        duration: Influx.FieldType.FLOAT,
      },
      tags: ["uid"],
    },
  ],
});

influx.getDatabaseNames().then((names) => {
  if (!names.includes(process.env.INFLUXDB_DATABASE)) {
    return influx.createDatabase(process.env.INFLUXDB_DATABASE);
  }
});

app.use(bodyParser.json());

app.post("/", async (req, res) => {
  let response = null;

  const uid = req.body.uid;
  const target = req.body.target;
  try {
    response = await ping.promise.probe(target);
  } catch (e) {
    console.log("error in icmp test", e);
    response = e.response;
  } finally {
    await influx.writePoints([
      {
        measurement: "icmp_test",
        tags: {
          uid: uid,
        },
        fields: {
          is_alive: response.alive,
          duration: response.avg == 'unknown' ? 1000 : parseFloat(response.avg).toFixed(2),
        },
      },
    ]);
  }
  res.send("OK");
});

app.listen(process.env.PORT);
