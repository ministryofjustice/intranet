// std lib
const crypto = require('crypto');
// npm
const cookieParser = require("cookie-parser");
const express = require('express');
const { createProxyMiddleware } = require('http-proxy-middleware');

const app = express();
const port = 80;
const publicKey = process.env.CLOUDFRONT_PUBLIC_KEY;

// Undo the character substitution that was done in php.
const revertCharacterSubstitution = (str) => {
    return str.replace(/-/g, '+').replace(/_/g, '=').replace(/~/g, '/');
}

// Middleware to get the cookies.
app.use(cookieParser());

// Middleware to validate the CloudFront cookies.
app.use(function (req, res, next) {

    const cookies = req.cookies
    
    if(!cookies['CloudFront-Key-Pair-Id'] || !cookies['CloudFront-Policy'] || !cookies['CloudFront-Signature']) {
        console.log('Missing cloudfront cookie(s)');
        return res.status(401).end();
    }
    
    // Get the cookies, and undo the character substitution that was done in php.
    const policyBase64 = revertCharacterSubstitution(cookies['CloudFront-Policy']);
    const signatureBase64 = revertCharacterSubstitution(cookies['CloudFront-Signature']);
    const verifier = crypto.createVerify('RSA-SHA1');

    // Validate the signature
    verifier.update(policyBase64, 'base64');
    const isValid = verifier.verify(publicKey, signatureBase64, 'base64');

    if (!isValid) {
        console.log('Invalid cloudfront signature');
        return res.status(401).end();
    }

    next();
});

// Middleware to proxy all the requests to the minio server.
app.use(
    createProxyMiddleware({
        target: `http://minio:9000/${process.env.S3_BUCKET_NAME}`,
    }),
);

// Start the server.
app.listen(port, () => {
    console.log(`cdn listening on port ${port}`)
}) 