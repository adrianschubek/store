<?php
/**
 * Copyright (c) 2020.
 * Adrian Schubek
 * https://adriansoftware.de
 */

if (!function_exists("store")) {
    function store($data = []): adrianschubek\Store\Store
    {
        return new adrianschubek\Store\Store($data);
    }
}