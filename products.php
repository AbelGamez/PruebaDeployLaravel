<?php

// Obtener datos JSON de la API
$data = json_decode(file_get_contents('https://bymykel.github.io/CSGO-API/api/en/skins.json'));

// Abrir archivo CSV en modo escritura
$fp = fopen('products.csv', 'w');

// Escribir encabezados en el archivo CSV
// fputcsv($fp, array('id', 'weapon_name', 'category_name', 'pattern_name', 'min_float', 'rarity_name', 'rarity_color', 'stattrak', 'wear_names', 'description', 'image'));

// Contador para el ID
$ID_counter = 1;

// Recorrer cada elemento en el array de datos
foreach ($data as $item) {
    // Obtener los valores de los campos
    $weapon_name = $item->weapon->name;

    $category_name = $item->category->name;

    $pattern_name = isset($item->pattern->name) ? $item->pattern->name : 'null';

    $rarity_name = $item->rarity->name;

    $rarity_color = $item->rarity->color;

    $stattrak = isset($item->stattrak) && $item->stattrak ? 1 : 0;

    // Obtener los nombres de los desgastes
    $wear_names = array();
    if (!empty($item->wears)) {
        foreach ($item->wears as $wear) {
            $wear_names[] = $wear->name;
        }
    } else {
        $wear_names[] = 'null';   
    }
    $wear_names_str = implode(', ', $wear_names);

    // Obtener la descripción y la imagen
    $description = str_replace(array("\\", "\\n", "nn", "<i>", "</i>"), '', $item->description);

    $image = $item->image;

    // Escribir en el archivo CSV (FALTA AÑADIR EL $array_wear_names)
        fputcsv($fp, array($ID_counter, $weapon_name, $category_name, $pattern_name, $rarity_name, $rarity_color, $description, $image));

    // Incrementar el contador de ID
    $ID_counter++;
}

// Cerrar el archivo CSV
fclose($fp);

echo "CSV generado correctamente.";

?>