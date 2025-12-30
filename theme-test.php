<!DOCTYPE html>
<html>
<head>
    <title>Theme Test</title>
    <style>
        :root {
            /* Default colors */
            --brand-gold: #F4B41A;
            --brand-amber: #F28C28;
            --brand-burnt: #E36F2D;
            --brand-crimson: #C73A2B;
        }
        
        .test-box {
            background: var(--brand-gold);
            color: white;
            padding: 20px;
            margin: 10px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
        }
        
        .test-box-amber {
            background: var(--brand-amber);
        }
        
        .test-box-burnt {
            background: var(--brand-burnt);
        }
        
        .test-box-crimson {
            background: var(--brand-crimson);
        }
    </style>
</head>
<body>
    <h1>Theme Color Test</h1>
    <div class="test-box">Default Gold: var(--brand-gold)</div>
    <div class="test-box test-box-amber">Default Amber: var(--brand-amber)</div>
    <div class="test-box test-box-burnt">Default Burnt: var(--brand-burnt)</div>
    <div class="test-box test-box-crimson">Default Crimson: var(--brand-crimson)</div>
    
    <?php
        // Test if theme colors are loaded from database
        $activeTheme = \App\Models\ThemeColor::getActive();
        if ($activeTheme) {
            echo '<style>';
            echo ':root {';
            echo '  --brand-gold: ' . $activeTheme->brand_gold . ';';
            echo '  --brand-amber: ' . $activeTheme->brand_amber . ';';
            echo '  --brand-burnt: ' . $activeTheme->brand_burnt . ';';
            echo '  --brand-crimson: ' . $activeTheme->brand_crimson . '; 
            echo '}';
            echo '</style>';
            echo '<script>';
            echo 'console.log("Theme loaded: ' . $activeTheme->name);';
            echo 'console.log("Brand Gold:", "' . $activeTheme->brand_gold . '");';
            echo '</script>';
        }
    ?>
</body>
</html>
