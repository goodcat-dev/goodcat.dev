---
title: 'View localizzate'
description: 'Organizza le view Blade specifiche per lingua in cartelle con il nome del locale.'
---

## Struttura delle cartelle

Metti ogni view localizzata in una cartella con il nome del suo locale:

```txt
/resources/views
├── example.blade.php
├── /it
│   └── example.blade.php
└── /es
    └── example.blade.php
```

## Renderizzare una view localizzata

Chiama `view('example')` come sempre. Quando il locale corrente è `it`, viene renderizzata `resources/views/it/example.blade.php` se esiste, altrimenti `resources/views/example.blade.php`.
