<?php

//namespace graficos;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author leoviquez
 */
class graficos {

    /**
     * 
     * @param int $x : Ancho
     * @param inty $y : Alto
     * @return image : i magen resultante
     */
    function crearImagen($x, $y) {
        $img = imagecreatetruecolor($x, $y);

        $trans = imagecolorallocatealpha($img, 255, 255, 255, 127);
        $red = imagecolorallocatealpha($img, 255, 0, 0, 63);
        $white = imagecolorallocatealpha($img, 255, 248, 246, 63);
        imagefilltoborder($img, 0, 0, $trans, $trans);
        imagesavealpha($img, true);

        //       imagefilledrectangle($img, 0, 0, 200, 200, $red);
//        imagefilledrectangle($img, 100, 100, 300, 300, $blue);

        $host = 'localhost';
        $db = 'cursoGIS';
        $usr = 'postgres';
        $pass = '12345';

        $strconn = "host=$host port=5432 dbname=$db user=$usr password=$pass";
        $conn = pg_connect($strconn) or die("Error de Conexion con la base de datos");
       
        //consulta para recuperar los valores x,y de los caminos a dibujar
        $query = "select c.gid gid,
	string_agg(CAST(((ST_X(ST_GeometryN(c.geom,1))-296480.57186013)/560.63136290052) as varchar(100)),', ') x,
	string_agg(CAST((640 - (ST_Y(ST_GeometryN(c.geom,1))-889378.554139937)/560.63136290052) as varchar(100)),', ') y
from (select ((ST_DumpPoints((ST_GeometryN(geom,1)))).geom) geom, gid 
	FROM caminos) c
	group by gid";


        $result = pg_query($conn, $query) or die("Error al ejecutar la consulta");

        while ($row = pg_fetch_row($result)) {

            $arrayX = explode(", ", $row[1]);//todos lo x recuperados en un arreglo
            $arrayY = explode(", ", $row[2]);//todos los y recuperados en un arreglo

            for ($i = 0; $i < count($arrayX)-1; ++$i) {
                imageline($img, $arrayX[$i], $arrayY[$i], $arrayX[$i+1], $arrayY[$i + 1], $white);
            }
        }


        return ($img);
    }

}
