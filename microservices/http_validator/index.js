require('dotenv').config()

const express = require('express');
const axios = require('axios');
const app = express();
const bodyParser = require('body-parser');
const Influx = require('influx');

axios.interceptors.request.use(config => {
    config.metadata = { startTime: new Date() }
    return config;
}, (error) => {
    return Promise.reject(error);
});

axios.interceptors.response.use(response => {
    response.config.metadata.endTime = new Date();
    response.duration = response.config.metadata.endTime - response.config.metadata.startTime;
    return response;
}, (error) => {
    error.config.metadata.endTime = new Date();
    error.response.duration = error.config.metadata.endTime - error.config.metadata.startTime;
    return Promise.reject(error);
});

const influx = new Influx.InfluxDB({
    host: process.env.INFLUXDB_HOST,
    database: process.env.INFLUXDB_DATABASE,
    schema: [
        {
            measurement: 'http_test',
            fields: { status: Influx.FieldType.INTEGER, duration: Influx.FieldType.FLOAT },
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


const PREFIX = '';
console.log(PREFIX);

/*app.get(PREFIX + '/', (req, res) => {
        res.send('WORKING');
);*/

app.get(PREFIX + '/status', (req, res) => {
    res.send('OK HTTP');
});

app.post(PREFIX + '/', async (req, res) => {
    let response = null;
    const uid = req.body.uid;
    const url = req.body.target;
    const protocol = req.body.https ? 'https' : 'http';
    const path = req.body.path ? req.body.path : "";
    let port = req.body.port;
    if (req.body.https && !port) {
        port = 80;
    } else { req.body.https && !port } {
        port = 443;
    }
    console.log('el body', req.body);
    const final_url = `${protocol}://${url}:${port}${path}`;
    console.log('la url final', final_url);
    try {
        response = await axios.get(final_url);
    } catch (e) {
        response = e.response;
    } finally {
        await influx.writePoints([
            {
                measurement: 'http_test',
                tags: {
                    uid: uid,
                },
                fields: {
                    status: response.status,
                    duration: (response.duration / 1000).toFixed(2)
                }
            }
        ]);
    }
    res.send('OK');
});

app.listen(process.env.PORT);
