# Color Scheme Replacement Script
# Replaces brown colors with blue/green/orange palette

$directories = @(
    "c:\xampp\htdocs\UphoCare\views\customer",
    "c:\xampp\htdocs\UphoCare\views\admin",
    "c:\xampp\htdocs\UphoCare\views\control_panel"
)

foreach ($dir in $directories) {
    Write-Host "Processing directory: $dir"
    Get-ChildItem -Path $dir -Filter *.php | ForEach-Object {
        $file = $_.FullName
        Write-Host "  Updating: $($_.Name)"
        
        $content = Get-Content $file -Raw
        $content = $content -replace '#8B4513', '#1F4E79'
        $content = $content -replace '#654321', '#0F3C5F'
        $content = $content -replace '#A0522D', '#4CAF50'
        $content = $content -replace '#CD853F', '#F57C00'
        
        Set-Content -Path $file -Value $content -NoNewline
    }
}

Write-Host "Color replacement complete!"
