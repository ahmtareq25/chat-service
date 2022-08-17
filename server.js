require('dotenv').config();
var app = require('express')();
var mysql = require('mysql');
var http = require('http').Server(app);

const io = require('socket.io')(http, {
    cors: { origin: "*"}
});
var Redis = require('ioredis');
var redis = new Redis();
redis.psubscribe(['*']);

// redis subscribe to all channels
redis.on('pmessage', function(pattern, channel, message) {
    message = JSON.parse(message);
    io.emit(channel, message.data);
});

// mysql connection
var con = mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE
});


// socket connection
io.on('connection', (socket) => {
    console.log('connection established');
    socket.on('emit-data',(res)=>{
        if(res.type=='message-seen'){
            let message_id=res.message_id;
            let read_by=res.read_by;
            var sql = `update chat_messages set read_by=case when read_by is null then '[${read_by}]'  else JSON_ARRAY_APPEND(read_by,'$',${read_by}) end where id=${message_id}`;
            // console.log(sql);
            con.query(sql, function (err, result) {
                console.log(result)
                if (err) throw err;
            });
        }
    });
    socket.on('disconnect', (socket) => {
        console.log('Disconnect');
    });
});
http.listen(3000, function(){
    console.log('Listening on Port 3000');
});
