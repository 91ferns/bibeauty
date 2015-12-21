'use strict';

var Busboy = require('busboy');

var Attachment = require('../../models/attachment');


module.exports = function (router) {

  router.get('/', function (req, res) {
    Attachment.find({})
      .then(function(attachments) {
        res.json(attachments);
      })
      .catch(function(err) {
        res.json(err);
      });
  });

  router.post('/new', function(req, res) {

    var attachment;
    var busboy = new Busboy({ headers: req.headers });

    busboy.on('file', function(fieldname, file, filename, encoding, mimetype) {
      // Make the attachment and then pipe it
      Attachment.createFromRawFileData(file, filename, encoding, mimetype)
        .then(function(newAttachment) {
          attachment = newAttachment;
          file.pipe(attachment.createS3WriteStream());
        });
    });

    busboy.on('finish', function() {
      res.json(201, attachment);
    });

    req.pipe(busboy);


  });

};
