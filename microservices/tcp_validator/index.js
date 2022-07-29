require('dotenv').config()

const express = require("express");
const app = express();
const bodyParser = require("body-parser");
const Influx = require("influx");
const net = require("net");
const { PromiseSocket } = require("promise-socket");
const dgram = require("dgram");
const { performance } = require("perf_hooks");

const influx = new Influx.InfluxDB({
  host: process.env.INFLUXDB_HOST,
  database: process.env.INFLUXDB_DATABASE,
  schema: [
    {
      measurement: "tcp_udp_test",
      fields: {
        status: Influx.FieldType.BOOLEAN,
        duration: Influx.FieldType.FLOAT,
        responseText: Influx.FieldType.STRING,
      },
      tags: ["uid"],
    },
  ],
});

const connectionTimeout = 5000;

influx.getDatabaseNames().then((names) => {
  if (!names.includes(process.env.INFLUXDB_DATABASE)) {
    return influx.createDatabase(process.env.INFLUXDB_DATABASE);
  }
});

app.use(bodyParser.json());

const handleTCPVerification = async (uid, url, port, writeText) => {
  let response = false;
  let responseText = "";
  let duration = 0;

  const socket = new net.Socket();
  const promiseSocket = new PromiseSocket(socket);

  promiseSocket.setTimeout(connectionTimeout);

  try {
    let startTime = performance.now();

    await promiseSocket.connect({ port: port, host: url });
    console.log("Luego de conectarse");
    let bytes = await promiseSocket.write(Buffer.from(writeText)); // Send text
    responseText = await promiseSocket.read(); // Wait for response
    let endTime = performance.now();

    duration = endTime - startTime;
    response = true; // Successfull request

    console.log("El estatus", bytes, "el text", responseText.toString());

    await promiseSocket.end();
  } catch (e) {
    console.error("Hubo Error ", e);
  } finally {
    console.log(response)
    await influx.writePoints([
      {
        measurement: "tcp_udp_test",
        tags: {
          uid: uid,
        },
        fields: {
          status: response,
          duration: duration,
          responseText: responseText,
        },
      },
    ]);
    console.log(response);
  }
};

const promiseWithTimeout = (timeoutMs, promise, failureMessage, client) => {
  const timeoutPromise = new Promise((resolve, reject) => {
    setTimeout(() => reject(new Error(failureMessage)), timeoutMs);
  });

  return Promise.race([promise(client), timeoutPromise]).then((result) => {
    return result;
  });
};

const writeUDPData = (client, data, port, url) => {
  return new Promise((resolve, reject) => {
    client.send(data, port, url, function (error) {
      if (error) {
        client.close();
        return reject();
      }
      console.log("Data sent !!!");
      return resolve(true);
    });
  });
};

const readUDPData = (client) => {
  return new Promise((resolve, reject) => {
    client.on("message", function (msg, info) {
      console.log("Data received from server : " + msg.toString());
      console.log(
        "Received %d bytes from %s:%d\n",
        msg.length,
        info.address,
        info.port
      );
      resolve(msg.toString());
    });
  });
};

const handleUDPVerification = async (uid, url, port, writeText) => {
  let response = false;
  let responseText = "";
  let duration = 0;

  const udpClient = dgram.createSocket("udp4");
  console.log("Antes de comenzar");
  try {
    let startTime = performance.now();

    const status = await writeUDPData(
      udpClient,
      Buffer.from(writeText),
      port,
      url
    ); // Send text

    responseText = await promiseWithTimeout(
      connectionTimeout,
      readUDPData,
      "Timeout in udp data",
      udpClient
    ); // Wait for response

    let endTime = performance.now();

    duration = endTime - startTime;
    response = true; // Successfull request

    udpClient.close();
    console.log("El status", status, "el text", responseText.toString());
  } catch (e) {
    console.error("Hubo Error ", e);
  } finally {
    await influx.writePoints([
      {
        measurement: "tcp_udp_test",
        tags: {
          uid: uid,
        },
        fields: {
          status: status,
          duration: duration,
          responseText: responseText,
        },
      },
    ]);
    console.log(response);
  }
};

app.post("/", async (req, res) => {
  const uid = req.body.uid;
  const url = req.body.url;
  let port = req.body.port;
  const writeText = req.body.writeText;
  const udp = req.body.udp;
  if (!port) port = 80;

  console.log('La request', req.body);
  //   const uid = "123";
  //   const url = "localhost";
  //   const port = 2399;
  //   const writeText = "testt";
  //   const udp = true;

  if (udp) {
    await handleUDPVerification(uid, url, port, writeText);
  } else {
    await handleTCPVerification(uid, url, port, writeText);
  }

  res.send("OK");
});

app.listen(process.env.PORT);
