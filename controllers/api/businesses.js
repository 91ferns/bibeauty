'use strict';

var Business = require('../../models/business');

module.exports = function (router) {

  router.get('/', function (req, res) {
    Business.find({})
      .then(function(businesses) {
        res.json(businesses);
      })
      .catch(function(err) {
        res.json(err);
      });
  });

};
