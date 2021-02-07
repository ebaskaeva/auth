RUN THE APP


git clone current repo

cd docker

docker-compose up

 
the app will be accessible via http://localhost:80



REGISTER AND LOG IN

with the help of web GUI

By default registration grants a user role



API EXAMPLES

LIST USERS

curl -XGET 'http://127.0.0.1:80/api/users'

CREATE USER

curl -XPOST -d '{"email": "email@example.com", "roles":"ROLE_ADMIN", "name": "admin", "password": "12345"}' 'http://127.0.0.1:80/api/users'

MODIFY USER

curl -XPUT -d '{"roles":"ROLE_ANOTHER", "name": "NEWname"} ' 'http://127.0.0.1:80/api/users/1'

DELETE USER

curl -XDELETE 'http://127.0.0.1:80/api/users/3
