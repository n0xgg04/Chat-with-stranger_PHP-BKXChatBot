#!/bin/bash

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo "🚀 Deploying Chat Bot Monorepo to Vercel"
echo "========================================"
echo "Current directory: $(pwd)"
echo ""

MANAGER_DIR="apps/manager"
API_DIR="apps/api"

read -p "Deploy to production? (y/N): " -n 1 -r
echo
PROD_FLAG=""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    PROD_FLAG="--prod"
    echo "✅ Will deploy to PRODUCTION"
else
    echo "✅ Will deploy to PREVIEW"
fi

echo ""
echo "📦 Deploying Manager (Next.js)..."
echo "--------------------------------"
cd $MANAGER_DIR
vercel $PROD_FLAG
MANAGER_EXIT_CODE=$?

cd "$SCRIPT_DIR"

if [ $MANAGER_EXIT_CODE -eq 0 ]; then
    echo "✅ Manager deployed successfully!"
else
    echo "❌ Manager deployment failed!"
    exit 1
fi

echo ""
echo "📦 Deploying API (NestJS)..."
echo "----------------------------"
cd $API_DIR
vercel $PROD_FLAG
API_EXIT_CODE=$?

cd "$SCRIPT_DIR"

echo ""
echo "========================================"
echo "🎉 All services deployed successfully!"
echo "========================================"
echo ""
echo "📝 Next steps:"
echo "1. Check deployments: vercel ls"
echo "2. View logs: vercel logs [url]"
echo "3. Set domains: vercel domains"

