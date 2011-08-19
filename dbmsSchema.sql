create table events (
id INT (11) NOT NULL AUTO_INCREMENT,
name VARCHAR (255),
description TEXT,
events_date DATE,
url VARCHAR (255),
type VARCHAR (255),
region INT(4),
venue_name VARCHAR (255),
venue_description TEXT,
venue_url VARCHAR (255),
venue_address TEXT,
venue_phone VARCHAR (1024),
venue_email VARCHAR (1024),
status VARCHAR (1024),
CONSTRAINT `region` FOREIGN KEY region REFERENCES regions(id),
PRIMARY KEY (id)
);

create table extra_fields (
object_id INT (11) NOT NULL AUTO_INCREMENT,
object_type VARCHAR (45),
field_name VARCHAR (1024),
content BLOB
);

create table interests (
id INT (11) NOT NULL AUTO_INCREMENT,
name VARCHAR (255),
category VARCHAR (255),
description TEXT,
PRIMARY KEY (id)
);

creat table organisations (
id INT (11) NOT NULL AUTO_INCREMENT,
name VARCHAR (255),
description TEXT,
url VARCHAR (255),
address TEXT,
phone VARCHAR (45),
fax VARCHAR (255),
email VARCHAR (255),
region INT (4),
status VARCHAR (1024),
CONSTRAINT `region` FOREIGN KEY region REFERENCES regions(id),
PRIMARY KEY (id)
);

create table people_interests(
people_id INT (11) NOT NULL AUTO_INCREMENT,
interest_id INT (11) NOT NULL AUTO_INCREMENT,
CONSTRAINT `people` FOREIGN KEY people_id REFERENCES people(id),
CONSTRAINT `interest` FOREIGN KEY interest_id REFERENCES interests(id),
PRIMARY KEY (people_id, interest_id)
);

create table people (
id INT (11) NOT NULL AUTO_INCREMENT,
firstname VARCHAR (255),
lastname VARCHAR (255),
email VARCHAR (1024),
email2 VARCHAR (1024),
peopleStatement TEXT,
confirmed BINARY,
url VARCHAR (1024),
image VARCHAR (1024),
image_caption VARCHAR (1024),
region INT (4)
gallery_image VARCHAR (1024),
salt VARCHAR (255),
passwd VARCHAR (1024),
status VARCHAR (1024),
cookiehash VARCHAR (100) NOT NULL DEFAULT 'nudda',
cookietime VARCHAR (20),
cookiesalt VARCHAR (30),
CONSTRAINT `region` FOREIGN KEY region REFERENCES regions(id),
PRIMARY KEY (id)
);

create table people_events (
people_id INT (11) NOT NULL AUTO_INCREMENT,
event_id INT (11) NOT NULL AUTO_INCREMENT,
type VARCHAR (255),
CONSTRAINT `people` FOREIGN KEY people_id REFERENCES people(id),
CONSTRAINT `publications` FOREIGN KEY publications_id REFERENCES publications(id)
PRIMARY KEY (people_id, event_id)
);

create table people_organisations (
people_id INT (11) NOT NULL AUTO_INCREMENT,
organisation_id INT (11) NOT NULL AUTO_INCREMENT,
type VARCHAR (255),
CONSTRAINT `people` FOREIGN KEY people_id REFERENCES people(id),
CONSTRAINT `organisation` FOREIGN KEY organisation_id REFERENCES organisation(id),
PRIMARY KEY (people_id, organisation_id)
);

create table people_publications (
people_id INT (11) NOT NULL AUTO_INCREMENT,
publications_id INT (11) NOT NULL AUTO_INCREMENT,
CONSTRAINT `people` FOREIGN KEY people_id REFERENCES people(id),
CONSTRAINT `publications` FOREIGN KEY publications_id REFERENCES publications(id),
PRIMARY KEY (people_id, publications_id)
);

create table people_roles (
people_id INT (11) NOT NULL AUTO_INCREMENT,
role_id INT (11) NOT NULL AUTO_INCREMENT,
CONSTRAINT `people` FOREIGN KEY people_id REFERENCES people(id),
CONSTRAINT `roles` FOREIGN KEY role_id REFERENCES roles(id),
PRIMARY KEY (people_id, role_id)
);

create table projects (
id INT (11) NOT NULL AUTO_INCREMENT,
name VARCHAR (255),
project_date DATE,
funding VARCHAR (255),
description TEXT,
type TEXT,
url VARCHAR (255),
sub_title VARCHAR (255),
gallery_image VARCHAR (255),
region INT (4),
status VARCHAR (1024),
CONSTRAINT `region` FOREIGN KEY region REFERENCES regions(id),
PRIMARY KEY (id)
);

create table projects_organisations (
project_id INT (11) NOT NULL AUTO_INCREMENT,
organisation_id INT (11) NOT NULL AUTO_INCREMENT,
CONSTRAINT `organisation` FOREIGN KEY organisation_id REFERENCES organisation(id),
CONSTRAINT `project` FOREIGN KEY project_id REFERENCES project(id),
PRIMARY KEY (project_id, organisation_id)
);

create table projects_people (
projects_id INT (11) NOT NULL AUTO_INCREMENT,
people_id INT (11) NOT NULL AUTO_INCREMENT,
CONSTRAINT `people` FOREIGN KEY people_id REFERENCES people(id),
CONSTRAINT `projects` FOREIGN KEY projects_id REFERENCES projects(id),
PRIMARY KEY (projects_id, people_id)
);

create table publications (
id INT (11) NOT NULL AUTO_INCREMENT,
name VARCHAR (255),
publications_date DATE,
description TEXT,
region INT (4),
status VARCHAR (1024),
CONSTRAINT `region` FOREIGN KEY region REFERENCES regions(id),
PRIMARY KEY (id)
);

create table regions (
id INT (11) AUTO_INCREMENT,
name VARCHAR (255),
description TEXT,
PRIMARY KEY (id)
);

create table roles (
id INT (11) NOT NULL AUTO_INCREMENT,
name VARCHAR (255),
description TEXT,
PRIMARY KEY (id)
);

