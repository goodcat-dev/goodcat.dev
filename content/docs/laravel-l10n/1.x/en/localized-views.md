---
title: 'Localized Views'
description: 'Organize language-specific Blade views in folders named after their locale.'
---

## Folder structure

Place each localized view in a folder named after its locale:

```txt
/resources/views
├── example.blade.php
├── /it
│   └── example.blade.php
└── /es
    └── example.blade.php
```

## Rendering a localized view

Call `view('example')` as usual. When the current locale is `it`, it renders `resources/views/it/example.blade.php` if available, otherwise `resources/views/example.blade.php`.
