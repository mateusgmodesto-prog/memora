<?php

function conectar_banco(): PDO
{
    $host = 'db:/firebird/data/agenda.fdb';
    $user = 'SYSDBA';
    $password = 'masterkey';

    $pdo = new PDO(
        "firebird:dbname={$host};charset=UTF8",
        $user,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $pdo;
}