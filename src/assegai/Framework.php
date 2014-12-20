<?php

/**
 * This file is part of Assegai
 *
 * Copyright (c) 2013 - 2014 Guillaume Pasquet
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace assegai;

use assegai\injector;

class Framework
{
    protected $container;

    function __construct()
    {
        $this->container = new injector\Container();
        $this->container->loadConfFile(__DIR__ . '/dependencies.conf');
    }

    function setConfigPath($conf_path)
    {
        $this->conf_path = $conf_path;
    }

    function serve($request = null)
    {
        $engine = $this->container->give('engine');
        $engine->setConfiguration($this->conf_path);
        $engine->serve($request);
    }

    function run($conf_path = '')
    {
        $request = $this->container->give('request');
        $request->fromGlobals();
        
        $this->setConfigPath($conf_path);
        $this->serve($request);
    }

    function runuri($uri, $conf_path = '')
    {
        if($conf_path) {
            $this->setConfigPath($conf_path);
        }

        $request = $this->container->give('request');
        $request->setRoute($uri);
        $request->setWholeRoute($uri);
        $request->setMethod('GET');

        $this->serve($request);
    }
}
