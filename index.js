'use strict';

var express = require('express');
var kraken = require('kraken-js');
var mongoose = require('mongoose');
var configure = require('./config');

var session = require('express-session');
var RedisStore = require('connect-redis')(session);
var passport = require('passport');

var options, app;

/*
 * Create and configure application. Also exports application instance for use by tests.
 * See https://github.com/krakenjs/kraken-js#options for additional configuration options.
 */
options = {
    onconfig: function (config, next) {
        /*
         * Add any additional config setup or overrides here. `config` is an initialized
         * `confit` (https://github.com/krakenjs/confit/) configuration object.
         */

        //
        configure(config);
        app.use(session({
          store: new RedisStore(config.get('redis')),
          secret: config.get('session:secret')
        }));
        app.use(require('flash')());
        app.use(passport.initialize());
        app.use(passport.session());
        app.use(function(req, res, next) {
          res.locals.user = req.user || false; //console.log(req.user);
          next();
        });

        next(null, config);
    }
};

app = module.exports = express();

app.use(kraken(options));
app.on('start', function () {
    console.log('Application ready to serve requests.');
    console.log('Environment: %s', app.kraken.get('env:env'));
});
