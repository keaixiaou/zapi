#!/bin/bash

kill -9 `ps aux|grep main.php|awk '{print $2}'`
