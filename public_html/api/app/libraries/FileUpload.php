<?php
/* 
    FileUpload
    Esta clase provee metodos para manipular los archivos en el directorio
    web y tambien en funciones de carga
    */

class FileUpload
{

    private $files_identifier = "file_uploads";
    private $files_folder_path = APP_ROOT . "/../../";
    private $default_folder_path = "files/";
    private $final_folder_path = "";
    private $max_file_size_mb = 10;

    private $valid_file_extensiones = [
        'image/jpeg',
        'image/png'
    ];

    public function set_files_identifier($p_new_identifier)
    {
        $this->files_identifier = $p_new_identifier;
    }

    public function set_final_folder_path($p_final_folder_path)
    {
        $this->final_folder_path = $p_final_folder_path;
    }

    public function get_upload_file($position)
    {
        return [
            'name' => $this->get_upload_file_name($position),
            'tmp_name' => $this->get_upload_file_tmpname($position),
            'type' => $this->get_upload_file_type($position),
            'size' => $this->get_upload_file_size($position)
        ];
    }

    public function config_file_and_get_name($position)
    {
        $file_type = $this->get_upload_file_type($position);
        if (!in_array($file_type, $this->valid_file_extensiones)) {
            processLog("Se intento cargar un archivo con extension invalida: $file_type, posicion: $position");
            return null;
        }

        if ($this->get_upload_file_size_inmb($position) > $this->max_file_size_mb) {
            processLog("Se intento cargar un archivo demasiado grande: " . $this->get_upload_file_size_inmb($position));
            return null;
        }

        if ($file_type == "image/jpeg" || $file_type == "image/png") {
            $this->set_final_folder_path("img/");
            $filename = $this->get_random_filename($file_type);
            return $filename;
        }
        return null;
    }

    public function get_count_files_upload()
    {
        return isset($_FILES[$this->files_identifier]['name']) ?
            count($_FILES[$this->files_identifier]['name']) : 0;
    }

    public function show_files_upload_info()
    {
        echo var_dump($_FILES[$this->files_identifier]);
    }

    public function get_upload_file_name($position)
    {
        return $_FILES[$this->files_identifier]["name"][$position];
    }

    public function get_upload_file_tmpname($position)
    {
        return $_FILES[$this->files_identifier]["tmp_name"][$position];
    }

    public function get_upload_file_type($position)
    {
        return $_FILES[$this->files_identifier]["type"][$position];
    }

    public function get_upload_file_size($position)
    {
        return $_FILES[$this->files_identifier]["size"][$position];
    }

    public function get_upload_file_size_inmb($position)
    {
        return ($this->get_upload_file_size($position) / 1000) / 1000;
    }

    public function save_upload_file($position)
    {
        $filename = $this->config_file_and_get_name($position);
        $files_folder = $this->get_complete_files_path();
        if ($filename) {

            if (!file_exists($files_folder . $this->final_folder_path)) {
                mkdir($files_folder . $this->final_folder_path,  0755, true);
            }
            if (move_uploaded_file(
                $this->get_upload_file_tmpname($position),
                $files_folder . $this->final_folder_path . $filename
            )) {
                return $this->default_folder_path . $this->final_folder_path . $filename;
            }
        }
        return null;
    }

    public function save_all_uploads($max_files = null)
    {
        $files_count = $this->get_count_files_upload();
        $files_saved = [];
        processLog(json_encode($_FILES));
        if (!is_null($max_files) && $max_files > 0 && $files_count != 0) {
            $files_count = $max_files;
        }
        for ($x = 0; $x < $files_count; $x++) {
            if ($new_file_path = $this->save_upload_file($x)) {
                $new_file = [
                    'path' => $new_file_path,
                    'type' => pathinfo($new_file_path, PATHINFO_EXTENSION),
                    'name' => $this->get_upload_file_name($x)
                ];
                array_push($files_saved, $new_file);
            }
        }
        return array_to_obj($files_saved);
    }

    public function get_complete_files_path()
    {
        return $this->files_folder_path . $this->default_folder_path;
    }

    public function get_random_filename($extension)
    {
        if ($extension == "image/jpeg") {
            $extension = "jpg";
        } elseif ($extension == "image/png") {
            $extension = "png";
        }

        $filename = get_random_str(20) . "." . $extension;
        while (file_exists($this->default_folder_path . $this->final_folder_path . $filename)) {
            $filename = get_random_str(20) . "." . $extension;
        }

        return $filename;
    }

    // DELETE
    public function delete_file($url)
    {
        $file_url = $this->files_folder_path . $url;
        if (file_exists($file_url)) {
            unlink($file_url);
            return true;
        }
        return false;
    }
}
