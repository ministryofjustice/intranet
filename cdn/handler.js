// https://github.com/aws-samples/amazon-cloudfront-functions/tree/main/verify-jwt
const crypto = require('crypto');

const isCloudFront = !console.error;

//Response when JWT is not valid.
const response401 = {
    statusCode: 401,
    statusDescription: 'Unauthorised request'
};

function handler(event) {
    var request = event.request;


    // Can we get access to aws secrets manager here?

    //Secret key used to verify JWT token.
    //Update with your own key.
    var key = isCloudFront ? "r64abiHPmO1Zvtx8OkyDjmJny4GB9asp3msAdH7DKsrd66vwE/Y4dY8SO4uNVx0ISeDW1HoiYbgKkYa4+wTNOg==" : process.env.JWT_SECRET;

    // If no JWT token, then generate HTTP redirect 401 response.
    if (!request.cookies.jwt) {
        console.log("Error: No JWT cookie found.");
        return response401;
    }

    var jwtToken = request.cookies.jwt.value;

    try {
        _jwt_decode(jwtToken, key);
    }
    catch (e) {
        console.log(e);
        return response401;
    }

    //Remove the JWT from the query string if valid and return.
    // delete request.querystring.jwt;
    delete request.cookies.jwt;
    console.log("Valid JWT token");
    return request;
}



function _jwt_decode(token, key) {
    // check token
    if (!token) {
        throw new Error('No token supplied');
    }
    // check segments
    const segments = token.split('.');
    if (segments.length !== 3) {
        throw new Error('Not enough or too many segments');
    }

    // All segment should be base64
    let headerSeg = segments[0];
    let payloadSeg = segments[1];
    let signatureSeg = segments[2];

    // base64 decode and parse JSON
    // let header = JSON.parse(_base64urlDecode(headerSeg));
    let payload = JSON.parse(_base64urlDecode(payloadSeg));

    const signingMethod = 'sha256';
    const signingType = 'hmac';

    // Verify signature. `sign` will return base64 string.
    let signingInput = [headerSeg, payloadSeg].join('.');

    if (!_verify(signingInput, key, signingMethod, signingType, signatureSeg)) {
        throw new Error('Signature verification failed');
    }

    // Support for exp claims.
    if (payload.exp && Date.now() > payload.exp * 1000) {
        throw new Error('Token expired');
    }

    if(!payload.roles || !payload.roles.includes('reader')) {
        throw new Error('Token does not have required role');
    }

    return payload;
}

//Function to ensure a constant time comparison to prevent
//timing side channels.
function _constantTimeEquals(a, b) {
    if (a.length != b.length) {
        return false;
    }

    var xor = 0;
    for (var i = 0; i < a.length; i++) {
        xor |= (a.charCodeAt(i) ^ b.charCodeAt(i));
    }

    return 0 === xor;
}

function _verify(input, key, method, type, signature) {
    if (type === "hmac") {
        return _constantTimeEquals(signature, _sign(input, key, method));
    }
    else {
        throw new Error('Algorithm type not recognized');
    }
}

function _sign(input, key, method) {
    return crypto.createHmac(method, key).update(input).digest('base64url');
}

function _base64urlDecode(str) {
    return Buffer.from(str, 'base64url')
}

// CloudFront doesn't allow export of handler.
// This is an untested workaround.
// https://stackoverflow.com/a/78000817/6671505
if (!isCloudFront) {
    module.exports = handler;
}