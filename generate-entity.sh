#!/bin/bash
vendor/doctrine/doctrine-module/bin/doctrine-module orm:clear-cache:metadata
vendor/doctrine/doctrine-module/bin/doctrine-module orm:clear-cache:result
vendor/doctrine/doctrine-module/bin/doctrine-module orm:clear-cache:query
./vendor/doctrine/doctrine-module/bin/doctrine-module orm:convert-mapping -f --from-database  annotation ./entities