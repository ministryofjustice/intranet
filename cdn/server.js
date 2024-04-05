const cookieParser = require("cookie-parser");
const { createProxyMiddleware } = require('http-proxy-middleware');
var app = require('express')();

const handler = require('./handler.js');

const port = 80

app.use(cookieParser());

app.use(function (req, res, next) {

    const cookies = req.cookies.jwt ? { value: req.cookies.jwt } : {};

    const { statusCode, statusDescription } = handler({ request: { cookies } });

    if (statusCode === 401) {
        return res.status(401).end();
    }

    // return res.send(401);

    next();
});

app.use(
    createProxyMiddleware({
        target: `http://minio:9000/${process.env.S3_BUCKET_NAME}`,
    }),
);

app.listen(port, () => {
    console.log(`cdn listening on port ${port}`)
}) 