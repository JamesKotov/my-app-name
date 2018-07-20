# A candidate test for Paxum

## Task description

We are building a competitor to Amazon S3 offering, You are to create an API that will support
the following:
  1. List the existing folders
      Content of the bucket can contain other folders and files
      There is no limit on the number of items within a folder
      There is no limit to number of subfolders
  2. Manage folders
      You should ensure the following constrains:
        the name of the folder is uniq between siblings
        deleting a folder will automatically delete all of the content within it
  3. File Management
      Folder element is optional, if null should be placed as root
      Any file type and size is allowed

  Please keep the implementation as simple as possible given that part of face to face interview will be 
  refactoring of this code. Please document areas that are open to interpretation and potential debate so
we could make a point to discuss them during face to face interview

## Used frameworks and technologies:

1. PHP used as main application language;

2. MySQL used as database server;

3. Slim used as base framework for API application (https://www.slimframework.com);

4. Propel as ORM engine (http://propelorm.org).

## Application API

1. Folder listing

`GET /path/to/folder` - return json with status and folder contents, or with error code if path is not found

Success request example:

`GET /`

response

``{
    "status": "ok",
    "folder": "root",
    "subfolders": [
      "myfolder",
      "mysecondfolder",
      "myanotherfolder"
    ],
    "files": [
      "todo.txt",
      "thedoor"
    ]
  }``

Error response example:

`GET /path/not/exists`

response


``{
    "status": "error",
    "code": "path not found"
  }``
  
2. File content

`GET /path/to/file.ext` - returns file content

3. Making a folder

For making a folder send an POST request to parent folder, with content type `multipart/form-data`.
Name of creating folder passed in a `folder` field of request body

`POST /path/to/parent/folder`
 
In response you will get JSON with listing of created folder (it may already exists), or error code.


4. Uploading a file

For upload a file, send an POST request to parent folder, with content type `multipart/form-data`.
Uploading file must passed in a `file` field of request body

`POST /path/to/parent/folder`
 
In response you will get JSON with listing of file's parent folder, or error code, if file already exists, or has uploading errors.
  

5. Uploading a file to new folder

This is a combination of two previous methods. If you pass in request's body both `folder` and `file` fields, than subfolder will create first at requested path, and after that, file will be stored to created subfolder.


6. Deleting file

`DELETE /path/to/file` - deletes requested file (if it exists).

In response you will get JSON with listing file's folder.

7. Deleting folders 

`DELETE /path/to/folder` - recursively deletes requested folder with all child objects (subfolders and files - both in current folder and all its subfolders)
 
 In response you will get JSON with listing of parent folder.
 
## Task essential

While this app based on slim skeleton application and Propel generated code, so not all code is written manually by myself.

Files with manually written code are:

* storage-api/actions/StorageApi.php
* storage-api/actions/ActionGet.php
* storage-api/actions/ActionPost.php
* storage-api/actions/ActionDelete.php
* storage-api/propel.yml
* storage-api/schema.xml
* src/routes.php
* and edited public/index.php

## Install app

1. Clone repository form github

2. Run `composer install` to install app dependencies

3. Go to atorage-api folder and edit database connection params in propel.yml file

4. Run `propel config:convert` to convert config to runtime format

5. Run `mysqladmin -u root -p create storage_api` to create empty DB (if it's not exists yet)

6. Run `propel sql:insert` for create tables in DB

7. Go back to project root

8. Run `php -S localhost:8080 -t public` to start PHP internal webserver

9. Check url `http://localhost:8080` in your browser  
