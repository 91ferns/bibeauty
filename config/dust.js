'use strict';

var routes = require('./routes.json').routes;

module.exports = function(dust, options) {

  const assetPrefix = '/';

  dust.helpers.asset = function(chunk, context, bodies, params) {
    return chunk.write(assetPrefix + params.src);
  };

  dust.helpers.path = function(chunk, context, bodies, params) {
    return chunk.write(routes[params.name] || params.name);
  };

  dust.helpers.url = function(chunk, context, bodies, params) {
    return chunk.write(params.name);
  };

};
