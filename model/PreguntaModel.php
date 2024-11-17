<?php

class PreguntaModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function obtenerPreguntas()
    {
        return $this->database->query("SELECT p.id id_pregunta, p.pregunta, o.opcion1, o.opcion2, o.opcion3, o.opcion_correcta FROM preguntas p
        JOIN opciones o ON p.id = o.pregunta_id                  
        GROUP BY p.id");
    }

    public function obtenerPregunta($id)
    {
        return $this->database->query("SELECT p.pregunta, o.opcion1, o.opcion2, o.opcion3, o.opcion_correcta FROM preguntas p
        JOIN opciones o ON p.id = o.pregunta_id WHERE p.id = '$id'");
    }

    public function agregarPregunta($pregunta)
    {
        $sql = "INSERT INTO preguntas (pregunta, categoria_id, usuario_id) 
        VALUES ('$pregunta', 1, 1)";

        $this->database->execute($sql);

        $idPregunta = $this->database->getLastInsertId();

        return $idPregunta;
    }

    public function agregarOpciones($idPregunta, $opcion1, $opcion2, $opcion3, $opcionCorrecta)
    {
        $sql = "INSERT INTO opciones (pregunta_id, opcion1, opcion2, opcion3, opcion_correcta) 
                    VALUES ($idPregunta, '$opcion1', '$opcion2', '$opcion3', '$opcionCorrecta')";

        $this->database->execute($sql);
    }

    public function agregarPreguntaConOpciones($pregunta, $opcion1, $opcion2, $opcion3, $opcionCorrecta)
    {
        $idPregunta = $this->agregarPregunta($pregunta);

        $this->agregarOpciones($idPregunta, $opcion1, $opcion2, $opcion3, $opcionCorrecta);
    }

    public function modificarPregunta($id, $pregunta)
    {
        if ($pregunta !== "") {
            $sql = "UPDATE preguntas
            SET pregunta = '$pregunta'
            WHERE id = '$id'";

            $this->database->execute($sql);
        }
    }

    public function modificarOpciones($idPregunta, $opcion1, $opcion2, $opcion3, $opcionCorrecta)
    {
        if ($opcion1 !== "") {
            $sql = "UPDATE opciones
            SET opcion1 = '$opcion1'
            WHERE pregunta_id = '$idPregunta'";

            $this->database->execute($sql);
        }

        if ($opcion2 !== "") {
            $sql = "UPDATE opciones
            SET opcion2 = '$opcion2'
            WHERE pregunta_id = '$idPregunta'";

            $this->database->execute($sql);
        }

        if ($opcion3 !== "") {
            $sql = "UPDATE opciones
            SET opcion2 = '$opcion3'
            WHERE pregunta_id = '$idPregunta'";

            $this->database->execute($sql);
        }

        if ($opcionCorrecta !== "") {
            $sql = "UPDATE opciones
            SET opcion_correcta = '$opcionCorrecta'
            WHERE pregunta_id = '$idPregunta'";

            $this->database->execute($sql);
        }
    }

    public function modificarPreguntaConOpciones($idPregunta, $pregunta, $opcion1, $opcion2, $opcion3, $opcionCorrecta)
    {
        $this->modificarPregunta($idPregunta, $pregunta);

        $this->modificarOpciones($idPregunta, $opcion1, $opcion2, $opcion3, $opcionCorrecta);
    }
}
