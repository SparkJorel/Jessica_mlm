#!/bin/bash
# Health check script for Jessica MLM migration
# Run after each phase to verify application integrity

set -e

PHP=${PHP_BIN:-php}
echo "=== Jessica MLM Health Check ==="
echo "PHP: $($PHP -v | head -1)"
echo ""

echo "1. Clearing cache..."
$PHP bin/console cache:clear --no-warmup 2>&1
echo "   OK"

echo "2. Warming up cache..."
$PHP bin/console cache:warmup 2>&1
echo "   OK"

echo "3. Validating Doctrine schema..."
$PHP bin/console doctrine:schema:validate 2>&1 || echo "   WARNING: Schema validation issues detected"

echo "4. Checking routes..."
$PHP bin/console debug:router 2>&1 | head -20

echo "5. Running tests..."
$PHP bin/phpunit 2>&1 || echo "   WARNING: Some tests failed"

echo ""
echo "=== Health Check Complete ==="
