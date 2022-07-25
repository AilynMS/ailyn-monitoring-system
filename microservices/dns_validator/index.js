require('dotenv').config()

const express = require("express");
const dns = require("dns");
const dnsPromises = dns.promises;
const app = express();
const bodyParser = require("body-parser");
const Influx = require("influx");
const { performance } = require('perf_hooks');

const influx = new Influx.InfluxDB({
  host: process.env.INFLUXDB_HOST,//"192.168.0.101",
  database: process.env.INFLUXDB_DATABASE,
  schema: [
    {
      measurement: "dns_test",
      fields: {
        a_record: Influx.FieldType.STRING,
        duration: Influx.FieldType.FLOAT
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
  let duration = 0;

  const options = {
    hints: dns.ADDRCONFIG | dns.V4MAPPED,
  };
  const uid = req.body.uid;
  const target = req.body.target;
  try {
    let startTime = performance.now();
    response = await dnsPromises.lookup(target, options);
    let endTime = performance.now();
    duration = endTime - startTime;
    console.log('la duracion', duration);
  } catch (e) {
    console.log("error in dns test", e);
    response = e.response;
  } finally {
    await influx.writePoints([
      {
        measurement: "dns_test",
        tags: {
          uid: uid,
        },
        fields: {
          a_record: response.address,
          duration: duration
        },
      },
    ]);
  }
  res.send("OK");
});

app.listen(process.env.PORT);
