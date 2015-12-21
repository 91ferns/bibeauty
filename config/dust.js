'use strict';

var _ = require('lodash');
var routes = require('./routes.json').routes;

module.exports = function(dust, options) {

  const assetPrefix = '/';

  function buildPath(params) {
    var routeStart = routes[params.name] || params.name;
    var route = routeStart;

    // Now we need to do parameter replacement
    _.forEach(params, function(val, key) {
      // prefix it with a colon
      var routeParamKey = ':' + key;
      // now do the replace
      route.replace(routeParamKey, val);
    });

    return route;

  }

  dust.helpers.asset = function(chunk, context, bodies, params) {
    return chunk.write(assetPrefix + params.src);
  };

  dust.helpers.path = function(chunk, context, bodies, params) {
    return chunk.write(buildPath(params));
  };

  dust.helpers.url = function(chunk, context, bodies, params) {
    return chunk.write(buildPath(params));
  };

};
