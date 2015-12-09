'use strict';

module.exports = function (router) {

  router.get('/', function (req, res) {
    res.render('index', {});
  });

  router.get('/search', function(req, res) {
    res.render('search', {});
  });

};
