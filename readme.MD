
EXAMPLE USER CREATE
curl -XPOST -d '{"email": "email@12.3", "roles":"USER_ROLE", "name": "name", "password": "12345"} ' 'http://127.0.0.1:44071/api/users'


curl -XPUT -d '{"roles":"ANOTHER_ROLE", "name": "NEWname"} ' 'http://127.0.0.1:44071/api/users/1'

curl -XDELETE 'http://127.0.0.1:44071/api/users/3
