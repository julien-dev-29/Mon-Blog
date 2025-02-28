<?php

$texte = 'Je suis avec ma tente';
$texte2 = 'Voici une liste des invités

- Madame Jane Doe née le 3 avril 2010
- Monsieur John Doe née le 4 mars 1920
- Madame Marion Dae né pendant la 3ème tempête du 19 avril 1940';

file_put_contents('texte.txt', $texte2);

preg_match('/^- (Madame|Monsieur) .+né/', file_get_contents('texte.txt'), $matches);

file_put_contents('result.txt', $matches);
