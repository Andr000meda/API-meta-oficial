create table if not exists alumnos (
    id int primary key auto_increment,
    nombres varchar(255) not null,
    apellidos varchar(255) not null,
    matricula varchar(255) not null,
    numero_telefono varchar(255) not null,
    numero_seguro_social varchar(255) not null
);

create table if not exists documentos (
    id int primary key auto_increment,
    id_alumno int not null,
    nombre varchar(255) not null,
    url varchar(255) not null
);

create table if not exists sesiones (
    id int primary key auto_increment,
    numero_telefono varchar(255) not null,
    estado varchar(255)
)

alter table documentos add constraint fk_documentos_alumnos foreign key (id_alumno) references alumnos(id);

alter table sesiones add constraint unique_numero_telefono unique (numero_telefono);

alter table alumnos add constraint unique_numero_telefono unique (numero_telefono);