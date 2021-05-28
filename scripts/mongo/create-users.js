/**
 * Create root user and daily-dev user
 */

if(typeof dbName === 'undefined'){
    print('Missing argument: dbName');
    quit(1);
}

db = db.getSiblingDB('admin');
db.createUser({
    user: "root",
    pwd: "vagrant",
    roles: [ "root", "userAdminAnyDatabase", "dbAdminAnyDatabase", "readWriteAnyDatabase" ]
});

db = db.getSiblingDB(dbName);
db.createUser({
    "user" : "vagrant",
    "pwd" : "vagrant",
    "roles" : [{"role" : "readWrite", "db" : dbName}]
});
