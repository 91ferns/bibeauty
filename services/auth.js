'use strict';

var mongoose = require('mongoose');
var passport = require('passport');
var jwt = require('jsonwebtoken');
var expressJwt = require('express-jwt');
var compose = require('composable-middleware');

var _ = require('lodash');

var User = require('../models/user');

/**
 * Attaches the user object to the request if authenticated
 * Otherwise returns 403
 */
function isAuthenticated() {
  return compose()

    // Validate jwt
    .use(function(req, res, next) {
      if (!req.isAuthenticated()) {
        res.send(403);
      }
      next();
    });

    // We already attach the user to the request on generic requests
}

function authenticateWithBusinesses() {
  return compose()
    .use(isAuthenticated())
    .use(function(req, res, next) {
      req.user.getBusinesses()
        .then(function(businesses) {
          res.locals.businesses = businesses;
          next();
        });
    });
}

function authenticateWithBusiness() {
  return compose()
    .use(authenticateWithBusinesses())
    .use(function(req, res, next) {
      req.user.getBusiness(req.params.id, req.params.slug)
        .then(function(business) {
          res.locals.business = business;
          next();
        })
        .catch(next);
    });
}

/**
 * Checks if the user role meets the minimum requirements of the route
 */
function hasRole(roleRequired) {
  if (!roleRequired) {
    throw new Error('Required role needs to be set');
  }

  return compose()
    .use(isAuthenticated())
    .use(function meetsRequirements(req, res, next) {
      if (req.user.hasRole(roleRequired)) {
        next();
      } else {
        res.send(403);
      }
    });
}

/**
 * Parse through params
 */
function parseDynamicParams(dynamicParams, req) {
  var params = {};

  _.forIn(dynamicParams, function(value, key) {
    params[key] = req.params[value] || '';
  });

  return params;
}

// User related authentication
exports.user = {
  isAuthenticated: isAuthenticated,
  businesses: authenticateWithBusinesses,
  hasRole: hasRole,
};
