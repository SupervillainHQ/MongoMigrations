db = db.getSiblingDB("admin");
db.createUser({
    user: "root",
    pwd: "vagrant",
    roles: [ "root", "userAdminAnyDatabase", "dbAdminAnyDatabase", "readWriteAnyDatabase" ]
});

db = db.getSiblingDB('MmTest');
db.createUser({
    "user" : "vagrant",
    "pwd" : "vagrant",
    "roles" : [{"role" : "readWrite", "db" : "MmTest"}]
});
