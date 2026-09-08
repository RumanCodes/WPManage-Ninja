<?php
add_filter('fluentform/insert_response_data', function ($response_data, $form) {
    // 1. Ejecutar SOLO para tu Formulario de examen (Form ID: 5)
    if (intval($form->id) !== 5) {
        return $response_data;
    }

    // 2. Decodificar las respuestas que vienen ingresando
    $response = json_decode($response_data['response'], true);

    // 3. Buscar el porcentaje real en tu campo verificado "quiz-score"
    $quiz_score = '0';
    if (isset($response['quiz-score'])) {
        $quiz_score = $response['quiz-score']; // Ej: "90.00%"
    }

    // Limpiamos el texto eliminando el "%" para dejar el número puro
    $porcentaje = floatval(trim(str_replace('%', '', $quiz_score)));

    // Red de seguridad: si viene en cero en el testeo, calculamos según los puntos
    if ($porcentaje == 0 && isset($response['__quiz_total_points'])) {
        $puntos = floatval($response['__quiz_total_points']);
        $porcentaje = ($puntos / 20) * 100;
    }

    // 4. Aplicar la Escala Chilena Oficial con 80% de exigencia
    if ($porcentaje < 80) {
        $nota = (($porcentaje / 80) * 3) + 1;
    } else {
        $nota = ((($porcentaje - 80) / 20) * 3) + 4;
    }

    // Límites físicos de la escala chilena
    if ($nota > 7.0) $nota = 7.0;
    if ($nota < 1.0) $nota = 1.0;

    $estado_final = ($porcentaje >= 80) ? 'APROBADO' : 'ASISTENTE';
    $codigo_seguro = 'VAL-2026-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 5));

    // 5. GUARDADO FÍSICO ALINEADO: Inyectamos directo en tus tres campos reales
    $response['numeric_field'] = number_format($nota, 1, '.', ''); // Tu nota final real
    $response['porcentaje_logro'] = number_format($porcentaje, 0);
    $response['estado'] = $estado_final;
    $response['codigo_fmc_seguro'] = $codigo_seguro;

    // Devolvemos los datos modificados para el guardado permanente
    $response_data['response'] = json_encode($response);

    return $response_data;
}, 10, 2);
