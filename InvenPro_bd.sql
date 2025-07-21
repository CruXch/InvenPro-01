-- BD EN XAMPP

CREATE DATABASE BD_InvenPro;

USE BD_InvenPro;

CREATE TABLE Tbl_Roles (
    id_rol INT PRIMARY KEY,
    nombre_rol VARCHAR(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Tbl_Usuarios (
    id_usuario INT PRIMARY KEY,
    contrasena_usuario VARCHAR(20) NOT NULL,
    nombre_usuario VARCHAR(30) NOT NULL,
    apellido_usuario VARCHAR(30) NOT NULL,
    correo_usuario VARCHAR(35) NOT NULL,
    telefono_usuario VARCHAR(15) NOT NULL,
    id_rol INT NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES Tbl_Roles(id_rol) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Tbl_Subcategorias (
    id_subcategoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_subcategoria VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Tbl_Categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(20) NOT NULL,
    id_subcategoria INT, 
    FOREIGN KEY (id_subcategoria) REFERENCES Tbl_Subcategorias(id_subcategoria) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Tbl_Productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre_producto VARCHAR(40) NOT NULL,
    descripcion_producto VARCHAR(70) NOT NULL,
    marca_producto VARCHAR(25) NOT NULL,
    referencia_producto VARCHAR(30) NOT NULL,
    stock_producto INT NOT NULL,
    unidad_producto VARCHAR(15) NOT NULL,
    id_categoria INT NOT NULL, 
    FOREIGN KEY (id_categoria) REFERENCES Tbl_Categorias(id_categoria) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Tbl_Tomas_De_Inventario (
    id_toma INT AUTO_INCREMENT PRIMARY KEY,
    frecuencia_toma VARCHAR(15) NOT NULL,
    fecha_toma DATE NOT NULL, /* Este ya no iria en la base de datos*/
    unidades_toma INT NOT NULL,
    comentarios_toma VARCHAR(70)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Tbl_Productos_Usuarios (
    id_producto INT,
    id_usuario INT,
    PRIMARY KEY (id_producto, id_usuario),
    FOREIGN KEY (id_producto) REFERENCES Tbl_Productos(id_producto),
    FOREIGN KEY (id_usuario) REFERENCES Tbl_Usuarios(id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Tbl_Productos_Tomas_De_Inventario (
    id_producto INT,
    id_toma INT,
    PRIMARY KEY (id_producto, id_toma),
    FOREIGN KEY (id_producto) REFERENCES Tbl_Productos(id_producto),
    FOREIGN KEY (id_toma) REFERENCES Tbl_Tomas_De_Inventario(id_toma)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




USE BD_InvenPro;

INSERT INTO tbl_usuarios (id_usuario, contrasena_usuario, nombre_usuario, apellido_usuario, correo_usuario, telefono_usuario, id_rol) VALUES
(1118321987, 'DM64825v', 'Dayana', 'Vallejo', 'dayana@email.com', '3164967812', 1),
(1118562874, 'DF66225P', 'David', 'Perez', 'david@email.com', '3226598742', 2),
(1118603200, 'AF98725V', 'Felipe', 'Vargas', 'felipe@email.com', '3156984523', 3);



