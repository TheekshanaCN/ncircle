<?php
/** views/partials/flash.php  -  one-time messages. Layouts include this above the page content. */
foreach (Flash::all() as $flash) {
    echo component("alert", ["type" => $flash["type"], "message" => $flash["message"], "dismissible" => true]);
}
