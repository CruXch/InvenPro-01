
CREATE DATABASE BD_InvenPro;

USE BD_InvenPro;

CREATE TABLE Tbl_Roles (
    id_rol INT PRIMARY KEY,
    nombre_rol VARCHAR(15) NOT NULL
) 


CREATE TABLE Tbl_Usuarios (
    id_usuario INT PRIMARY KEY,
    contrasena_usuario VARCHAR(20) NOT NULL,
    nombre_usuario VARCHAR(30) NOT NULL,
    apellido_usuario VARCHAR(30) NOT NULL,
    correo_usuario VARCHAR(35) NOT NULL,
    telefono_usuario VARCHAR(15) NOT NULL,
    id_rol INT NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES Tbl_Roles(id_rol) 
) 


CREATE TABLE Tbl_Subcategorias (
    id_subcategoria INT PRIMARY KEY,
    nombre_subcategoria VARCHAR(20) NOT NULL
) 


CREATE TABLE Tbl_Categorias (
    id_categoria INT PRIMARY KEY,
    nombre_categoria VARCHAR(20) NOT NULL,
    id_subcategoria INT NULL, 
    FOREIGN KEY (id_subcategoria) REFERENCES Tbl_Subcategorias(id_subcategoria) 
) 


CREATE TABLE Tbl_Productos (
    id_producto INT PRIMARY KEY,
    nombre_producto VARCHAR(40) NOT NULL,
    descripcion_producto VARCHAR(70) NOT NULL,
    marca_producto VARCHAR(25) NOT NULL,
    referencia_producto VARCHAR(30) NOT NULL,
    stock_producto INT NOT NULL,
    unidad_producto VARCHAR(15) NOT NULL,
    id_categoria INT NOT NULL, 
    FOREIGN KEY (id_categoria) REFERENCES Tbl_Categorias(id_categoria) 
) 

CREATE TABLE tbl_Ingresos_Csv (
    id_ingreso INT AUTO_INCREMENT PRIMARY KEY,
    nombre_ingreso VARCHAR(40) NOT NULL,
    marca_ingreso VARCHAR(25) NOT NULL,
    stock_ingreso INT NOT NULL,
    fecha_ingreso DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_producto INT NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES tbl_productos(id_producto)
);


CREATE TABLE Tbl_Tomas_De_Inventario (
    id_toma INT AUTO_INCREMENT PRIMARY KEY,
    fecha_toma DATE NOT NULL,
    unidades_toma INT NULL,
    comentarios_toma VARCHAR(70),
    diferencia_toma INT NULL
) 


CREATE TABLE Tbl_Productos_Usuarios (
    id_producto INT,
    id_usuario INT,
    PRIMARY KEY (id_producto, id_usuario),
    FOREIGN KEY (id_producto) REFERENCES Tbl_Productos(id_producto),
    FOREIGN KEY (id_usuario) REFERENCES Tbl_Usuarios(id_usuario) 
) 


CREATE TABLE Tbl_Productos_Tomas_De_Inventario (
    id_producto INT,
    id_toma INT,
    PRIMARY KEY (id_producto, id_toma),
    FOREIGN KEY (id_producto) REFERENCES Tbl_Productos(id_producto),
    FOREIGN KEY (id_toma) REFERENCES Tbl_Tomas_De_Inventario(id_toma) 
) 


INSERT INTO tbl_roles (id_rol, nombre_rol) VALUES
(1, 'Administrador'),
(2, 'Contador'),
(3, 'Jefe');


INSERT INTO tbl_usuarios (id_usuario, contrasena_usuario, nombre_usuario, apellido_usuario, correo_usuario, telefono_usuario, id_rol) VALUES
(1118321987, 'DM64825v', 'Dayana', 'Vallejo', 'dayana@email.com', '3164967812', 1),
(1118562874, 'DF66225P', 'David', 'Perez', 'david@email.com', '3226598742', 2),
(1118603200, 'AF98725V', 'Felipe', 'Vargas', 'felipe@email.com', '3156984523', 3);






