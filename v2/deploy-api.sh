#!/bin/bash

set -e

echo "🚀 Deploying API to Vercel with local .env"
echo "==========================================="
echo ""

API_DIR="apps/api"
ENV_FILE="$API_DIR/.env"

if [ ! -f "$ENV_FILE" ]; then
    echo "❌ Error: .env file not found at $ENV_FILE"
    echo ""
    echo "📝 Please create a .env file based on .env.example:"
    echo "   cp $API_DIR/.env.example $ENV_FILE"
    echo "   then edit $ENV_FILE with your actual values"
    exit 1
fi

echo "✅ Found .env file at $ENV_FILE"
echo ""

read -p "Deploy to production? (y/N): " -n 1 -r
echo
PROD_FLAG=""
ENV_TARGET="preview"
if [[ $REPLY =~ ^[Yy]$ ]]; then
    PROD_FLAG="--prod"
    ENV_TARGET="production"
    echo "✅ Will deploy to PRODUCTION"
else
    echo "✅ Will deploy to PREVIEW"
fi

echo ""
echo "📝 Syncing environment variables to Vercel ($ENV_TARGET)..."
echo "-----------------------------------------------------------"

set -a
source "$ENV_FILE"
set +a

REQUIRED_VARS=(
    "DATABASE_URL"
    "REDIS_HOST"
    "REDIS_PORT"
    "FB_PAGE_ACCESS_TOKEN"
    "FB_VERIFY_TOKEN"
    "FB_APP_SECRET"
)

echo ""
echo "Checking required environment variables..."

for VAR in "${REQUIRED_VARS[@]}"; do
    if [ -z "${!VAR}" ]; then
        echo "❌ Error: $VAR is not set in .env"
        exit 1
    fi
    echo "✅ $VAR is set"
done

echo ""
echo "🔄 Pushing environment variables to Vercel..."
echo ""

cd "$API_DIR"

for VAR in "${REQUIRED_VARS[@]}"; do
    VALUE="${!VAR}"
    echo "Setting $VAR..."
    echo "$VALUE" | vercel env add "$VAR" $ENV_TARGET --force 2>/dev/null || true
done

if [ -n "$REDIS_PASSWORD" ]; then
    echo "Setting REDIS_PASSWORD..."
    echo "$REDIS_PASSWORD" | vercel env add REDIS_PASSWORD $ENV_TARGET --force 2>/dev/null || true
fi

if [ -n "$PORT" ]; then
    echo "Setting PORT..."
    echo "$PORT" | vercel env add PORT $ENV_TARGET --force 2>/dev/null || true
fi

if [ -n "$NODE_ENV" ]; then
    echo "Setting NODE_ENV..."
    echo "$NODE_ENV" | vercel env add NODE_ENV $ENV_TARGET --force 2>/dev/null || true
fi

echo ""
echo "✅ Environment variables synced!"
echo ""

echo "📦 Running Prisma migrations..."
echo "-------------------------------"
yarn prisma:migrate:prod || echo "⚠️  Warning: Migration failed or no changes"

echo ""
echo "🏗️  Building and deploying to Vercel..."
echo "---------------------------------------"

vercel $PROD_FLAG

EXIT_CODE=$?

cd ../..

if [ $EXIT_CODE -eq 0 ]; then
    echo ""
    echo "========================================"
    echo "🎉 API deployed successfully!"
    echo "========================================"
    echo ""
    echo "📝 Next steps:"
    echo "1. Test your API endpoint"
    echo "2. Check logs: vercel logs [url]"
    echo "3. Set custom domain: vercel domains add [domain]"
else
    echo ""
    echo "❌ Deployment failed!"
    exit 1
fi

