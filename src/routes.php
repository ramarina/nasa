<?php

return [
    ['GET', '/', ['Nasa\Controllers\Photos', 'showPhotos']],
    ['GET', '/index.php', ['Nasa\Controllers\Photos', 'showPhotos']],
    ['GET', '/photos/{rover}/{camera}/{dayRange}/{limit}', ['Nasa\Controllers\Photos', 'showPhotos']],
];