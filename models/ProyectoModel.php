<?php
/** 
 * Este modelo va servir para poder obtener los datos del servidor y poder enviarlos al cliente
 * */ 


class ProyectoModel {
    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
        customLog("Proyecto Model ha recibido conexión a la base de datos");
    }

    // Metodo para poder obtener datos del proyecto de un area
    public function obtenerDatosProyecto($idArea, $Year) {
        // Preparar la consulta SQL con el parámetro :Year en lugar de un año fijo
        $stmt = $this->pdo->prepare("
            SELECT * FROM fichatecnica
            JOIN catunimedida ON catunimedida.idUniMedida = fichatecnica.idUniMedida
            JOIN catunimedible ON catunimedible.idUniMedible = fichatecnica.idUniMedible
            JOIN catdimension ON catdimension.idDimension = fichatecnica.idDM
            JOIN catsentidoindicador ON catsentidoindicador.idSentidoIndicador = fichatecnica.idSI
            JOIN catclasificacion ON catclasificacion.idMeta = fichatecnica.idCM
            JOIN catfrecuenciamedicion ON catfrecuenciamedicion.idFreMed = fichatecnica.idFM
            WHERE fichatecnica.idArea = :idArea
            AND fichatecnica.idEjercicio = :Year
            ORDER BY fichatecnica.idIndicador
        ");
    
        // Enlazar los parámetros :idArea y :Year con los valores recibidos en la función
        $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
        $stmt->bindParam(':Year', $Year, PDO::PARAM_INT);
    
        // Ejecutar la consulta
        $stmt->execute();
    
        // Obtener todos los resultados
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Metodo para obtener variables de un indicador
    public function obtenerDatosVariables($idArea, $Year) {
        // Preparar la consulta SQL con los parámetros :idArea y :Year
        $stmt = $this->pdo->prepare("
            SELECT fichatecnica.idEjercicio AS idEje,
                fichatecnica.idPrograma AS idProg,
                fichatecnica.idArea AS idArea,
                fichatecnica.idIndicador AS idIndica,
                fichatecnica.idFin AS idF,
                fichatecnica.idProposito AS idP,
                fichatecnica.idComponente AS idC,
                fichatecnica.idActividad AS idA,
                programacionvariables.idTipoProgramacion AS tipoProg,
                programacionvariables.descripcion_v1 AS dV1,
                programacionvariables.valor_v1 AS vV1,
                programacionvariables.fuente_v1 AS fV1,
                programacionvariables.descripcion_v2 AS dV2,
                programacionvariables.valor_v2 AS vV2,
                programacionvariables.fuente_v2 AS fV2 
            FROM 
                fichatecnica
            JOIN 
                programacionvariables 
            ON 
                fichatecnica.idPrograma = programacionvariables.idPrograma
                AND fichatecnica.idArea = programacionvariables.idArea
                AND fichatecnica.idIndicador = programacionvariables.idIndicador
            WHERE 
                programacionvariables.idArea = :idArea
                AND programacionvariables.idEjercicio = :Year
            ORDER BY 
                fichatecnica.idIndicador
        ");
    
        // Enlazar los parámetros :idArea y :Year con los valores recibidos en la función
        $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
        $stmt->bindParam(':Year', $Year, PDO::PARAM_INT);
    
        // Ejecutar la consulta
        $stmt->execute();
    
        // Obtener todos los resultados
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Obtener Metas Programadas por el Area
    public function obtenerDatosDeMetasProgramadas($idArea, $year){
        $stmt = $this->pdo->prepare("
            SELECT 
                fichatecnica.idPrograma AS idProg,
                fichatecnica.idArea AS idArea,
                fichatecnica.idIndicador AS idIndica,
                fichatecnica.idFin AS idF,
                fichatecnica.idProposito AS idP,
                fichatecnica.idComponente AS idC,
                fichatecnica.idActividad AS idA,
                metaprogramada.m1_p,
                metaprogramada.m2_p,
                metaprogramada.m3_p,
                metaprogramada.m4_p,
                metaprogramada.ma_p,
                metaprogramada.vlb,
                metaprogramada.plb,
                metaprogramada.alb,
                metaprogramada.fichaHabilitadaMeta,
                metaprogramada.idEstado,
                metaprogramada.numOficio,
                metaprogramada.numFolio
            FROM 
                metaprogramada
            INNER JOIN 
                fichatecnica 
            ON 
                fichatecnica.idPrograma = metaprogramada.idPrograma 
                AND fichatecnica.idArea = metaprogramada.idArea 
                AND fichatecnica.idIndicador = metaprogramada.idIndicador
            WHERE 
                metaprogramada.idArea = :idArea 
                AND metaprogramada.idEjercicio = :year
        ");
    
        // Asigna los parámetros correctamente
        $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Funcion para poder obtener el seguimiento del proyecto del Area
    public function obtenerDatosSeguimiento($idArea){
        $stmt = $this->pdo->prepare("
            SELECT 
                ft.idEjercicio AS ejercicio,
                ft.idPrograma AS idProg,
                ft.idArea AS idArea,
                ft.idIndicador AS idIndica,
                ft.idFin AS idF,
                ft.idProposito AS idP,
                ft.idComponente AS idC,
                ft.idActividad AS idA,
                sa.resumenTrim1, sa.resumenTrim2, sa.resumenTrim3, sa.resumenTrim4,
                sa.indicadorTrim1, sa.indicadorTrim2, sa.indicadorTrim3, sa.indicadorTrim4,
                sa.metaTrim1, sa.metaTrim2, sa.metaTrim3, sa.metaTrim4,
                sa.justificaTrim1, sa.justificaTrim2, sa.justificaTrim3, sa.justificaTrim4,
                sa.mediosTrim1, sa.mediosTrim2, sa.mediosTrim3, sa.mediosTrim4,
                sa.hallazgosTrim1, sa.hallazgosTrim2, sa.hallazgosTrim3, sa.hallazgosTrim4,
                sa.mejoraTrim1, sa.mejoraTrim2, sa.mejoraTrim3, sa.mejoraTrim4,
                sa.evaluadorTrim1, sa.evaluadorTrim2, sa.evaluadorTrim3, sa.evaluadorTrim4
            FROM 
                fichatecnica ft
            LEFT JOIN 
                seguimientoadmin sa 
                ON ft.idArea = sa.idArea 
                AND ft.idIndicador = sa.idIndicador
            WHERE 
                ft.idArea = :idArea
        ");

        $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);

        
         $stmt->execute();
        
         return $stmt->fetchAll(PDO::FETCH_ASSOC);    
    }

    // Función para obtener las metas alcanzadas del área
    public function obtenerDatosMetasAlcanzadas($idArea) {
        $stmt = $this->pdo->prepare("
           SELECT 
            ft.idEjercicio AS ejercicio,
            ft.idPrograma AS idProg,
            ft.idArea AS idArea,
            ft.idIndicador AS idIndica,
            ft.idFin AS idF,
            ft.idProposito AS idP,
            ft.idComponente AS idC,
            ft.idActividad AS idA,
            la.MetaAlcanzadaTrim1, la.MetaAlcanzadaTrim2, la.MetaAlcanzadaTrim3, la.MetaAlcanzadaTrim4, la.MetaAlcanzadaTrim5,
            la.Cuausa1 as Causa1, la.Cuausa2 as Causa2, la.Cuausa3 as Causa3, la.Cuausa4 as Causa4, la.Cuausa5 as Causa5,
            la.Efecto1, la.Efecto2, la.Efecto3, la.Efecto4, la.Efecto5,
            la.Evidencia1, la.Evidencia2, la.Evidencia3, la.Evidencia4,
            la.Ruta1, la.Ruta2, la.Ruta3, la.Ruta4,
            la.Obs1_1, la.Obs1_2, la.Obs1_3, la.Obs1_4,
            la.Obs2_1, la.Obs2_2, la.Obs2_3, la.Obs2_4,
            la.seguiasm_1, la.seguiasm_2, la.seguiasm_3, la.seguiasm_4

            FROM 
                fichatecnica ft
            LEFT JOIN 
                logrosalcanzados la 
                ON ft.idPrograma = la.idPrograma 
                AND ft.idArea = la.idArea 
                AND ft.idIndicador = la.idIndicador
            WHERE 
                ft.idArea = :idArea;
        ");
        
        $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);    
    }

    public function obtenerTrimActivoArea($idArea){
        $stmt = $this->pdo->prepare('SELECT * FROM trimpermisosarea WHERE trimpermisosarea.idArea = :idArea');
        
        $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);    
    }

    public function obtenerLogros($idEjercicio, $idArea, $idTrimestre, $idIndicador){
        $stmt = $this->pdo->prepare('SELECT * FROM logros 
                                        WHERE logros.idEjercicio = :idEjercicio 
                                        AND logros.idArea = :idArea 
                                        AND logros.idTrimestre = :idTrimestre 
                                        AND logros.idIndicador = :idIndicador');

        $stmt->bindParam(':idEjercicio', $idEjercicio, PDO::PARAM_INT);
        $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
        $stmt->bindParam(':idTrimestre', $idTrimestre, PDO::PARAM_INT);
        $stmt->bindParam(':idIndicador', $idIndicador, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);   

    }

    public function registrarLogroArea(
        $idEjercicio,
        $idRamo,
        $idFuenteFinan,
        $idPrograma,
        $idArea,
        $idIndicador,
        $idTrimestre,
        $logros,
        $causa,
        $efecto,
        $obs1,
        $obs2
    ) {
        // Verificar si el registro ya existe
        if ($this->verificarLogro($idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idIndicador, $idTrimestre)) {
            // Actualizar registro existente
            $sqlUpdate = 'UPDATE logros 
                          SET logro = ?, causa = ?, efecto = ?, obs1 = ?, obs2 = ?
                          WHERE idEjercicio = ? AND idRamo = ? AND idFuenteFinan = ? 
                            AND idPrograma = ? AND idArea = ? AND idIndicador = ? AND idTrimestre = ?';
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([
                $logros,
                $causa,
                $efecto,
                $obs1,
                $obs2,
                $idEjercicio,
                $idRamo,
                $idFuenteFinan,
                $idPrograma,
                $idArea,
                $idIndicador,
                $idTrimestre
            ]);
    
            return "Registro actualizado exitosamente.";
        } else {
            // Insertar nuevo registro
            $sqlInsert = "INSERT INTO logros 
                            (idEjercicio, idRamo, idFuenteFinan, idPrograma, 
                             idArea, idIndicador, idTrimestre, logro, causa, 
                             efecto, obs1, obs2) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtInsert = $this->pdo->prepare($sqlInsert);
            $stmtInsert->execute([
                $idEjercicio,
                $idRamo,
                $idFuenteFinan,
                $idPrograma,
                $idArea,
                $idIndicador,
                $idTrimestre,
                $logros,
                $causa,
                $efecto,
                $obs1,
                $obs2
            ]);
    
            return "Registro insertado exitosamente.";
        }
    }
    
    public function verificarLogro(
        $idEjercicio,
        $idRamo,
        $idFuenteFinan,
        $idPrograma,
        $idArea,
        $idIndicador,
        $idTrimestre
    ) {
        $sql = "SELECT COUNT(*) as count FROM logros 
                WHERE idEjercicio = ? AND idRamo = ? AND idFuenteFinan = ? 
                  AND idPrograma = ? AND idArea = ? AND idIndicador = ? AND idTrimestre = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $idEjercicio,
            $idRamo,
            $idFuenteFinan,
            $idPrograma,
            $idArea,
            $idIndicador,
            $idTrimestre
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result['count'] > 0;
    }
    

    public function obtenerAspectosMejora($idArea, $idIndicador){
        $stmt = $this->pdo->prepare("
        SELECT
            seguimientoadmin.mejoraTrim1, 
            seguimientoadmin.mejoraTrim2, 
            seguimientoadmin.mejoraTrim3,
            seguimientoadmin.mejoraTrim4
            FROM seguimientoadmin
            WHERE 
            seguimientoadmin.idArea = :idArea AND seguimientoadmin.idIndicador = :idIndicador
        ");

        $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
        $stmt->bindParam(':idIndicador', $idIndicador, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);   
    }

    public function obtenerMir($idArea) {
        try {
            // Preparar la consulta SQL
            $stmt = $this->pdo->prepare("SELECT numCA, RN, Indicador, MV, supuestos, formula, cUniMedible, cUniMedida, cDimension, cSentidoIndicador, cMeta, cFreMed, idNivelIndicador, m1_p, m2_p, m3_p, m4_p, ma_p, MetaAlcanzadaTrim1, MetaAlcanzadaTrim2, MetaAlcanzadaTrim3, MetaAlcanzadaTrim4, Cuausa1, Cuausa2, Cuausa3, Cuausa4, Efecto1, Efecto2, Efecto3, Efecto4 
            FROM mir_uno LEFT JOIN 
            metaprogramada 
            ON mir_uno.idArea = metaprogramada.idArea 
            AND mir_uno.idPrograma = metaprogramada.idPrograma 
            AND mir_uno.idIndicador = metaprogramada.idIndicador 
            LEFT JOIN logrosalcanzados 
            ON mir_uno.idArea = logrosalcanzados.idArea 
            AND mir_uno.idIndicador = logrosalcanzados.idIndicador WHERE mir_uno.idArea = :idArea ORDER BY mir_uno.numCA");
    
            // Enlazar el parámetro
            $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
    
            // Ejecutar la consulta
            $stmt->execute();
    
            // Obtener los resultados
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Devolver los resultados
            return $resultados;
    
        } catch (PDOException $e) {
            // Manejo de errores
            echo "Error al obtener la MIR: " . $e->getMessage();
            return [];
        }
    }    
}