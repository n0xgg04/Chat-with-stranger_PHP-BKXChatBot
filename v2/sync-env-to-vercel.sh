#!/bin/bash

set -e

echo "🔄 Sync Environment Variables to Vercel"
echo "========================================"
echo ""

if [ $# -eq 0 ]; then
    echo "Usage: ./sync-env-to-vercel.sh [api|manager|all] [production|preview|development]"
    echo ""
    echo "Examples:"
    echo "  ./sync-env-to-vercel.sh api production"
    echo "  ./sync-env-to-vercel.sh manager preview"
    echo "  ./sync-env-to-vercel.sh all production"
    exit 1
fi

SERVICE=$1
ENV_TARGET=${2:-production}

sync_api_env() {
    local ENV_FILE="apps/api/.env"
    
    if [ ! -f "$ENV_FILE" ]; then
        echo "❌ Error: .env file not found at $ENV_FILE"
        exit 1
    fi
    
    echo "📝 Syncing API environment variables..."
    echo ""
    
    set -a
    source "$ENV_FILE"
    set +a
    
    cd apps/api
    
    declare -A ENV_VARS=(
        ["DATABASE_URL"]="$DATABASE_URL"
        ["REDIS_HOST"]="$REDIS_HOST"
        ["REDIS_PORT"]="$REDIS_PORT"
        ["REDIS_PASSWORD"]="$REDIS_PASSWORD"
        ["FB_PAGE_ACCESS_TOKEN"]="$FB_PAGE_ACCESS_TOKEN"
        ["FB_VERIFY_TOKEN"]="$FB_VERIFY_TOKEN"
        ["FB_APP_SECRET"]="$FB_APP_SECRET"
        ["PORT"]="$PORT"
        ["NODE_ENV"]="$NODE_ENV"
    )
    
    for KEY in "${!ENV_VARS[@]}"; do
        VALUE="${ENV_VARS[$KEY]}"
        if [ -n "$VALUE" ]; then
            echo "Setting $KEY..."
            echo "$VALUE" | vercel env add "$KEY" "$ENV_TARGET" --force 2>/dev/null || true
        fi
    done
    
    cd ../..
    echo "✅ API environment variables synced!"
}

sync_manager_env() {
    local ENV_FILE="apps/manager/.env.local"
    
    if [ ! -f "$ENV_FILE" ]; then
        echo "❌ Error: .env.local file not found at $ENV_FILE"
        exit 1
    fi
    
    echo "📝 Syncing Manager environment variables..."
    echo ""
    
    set -a
    source "$ENV_FILE"
    set +a
    
    cd apps/manager
    
    declare -A ENV_VARS=(
        ["NEXT_PUBLIC_API_URL"]="$NEXT_PUBLIC_API_URL"
        ["NEXT_PUBLIC_SUPABASE_URL"]="$NEXT_PUBLIC_SUPABASE_URL"
        ["NEXT_PUBLIC_SUPABASE_ANON_KEY"]="$NEXT_PUBLIC_SUPABASE_ANON_KEY"
    )
    
    for KEY in "${!ENV_VARS[@]}"; do
        VALUE="${ENV_VARS[$KEY]}"
        if [ -n "$VALUE" ]; then
            echo "Setting $KEY..."
            echo "$VALUE" | vercel env add "$KEY" "$ENV_TARGET" --force 2>/dev/null || true
        fi
    done
    
    cd ../..
    echo "✅ Manager environment variables synced!"
}

case "$SERVICE" in
    api)
        sync_api_env
        ;;
    manager)
        sync_manager_env
        ;;
    all)
        sync_api_env
        echo ""
        sync_manager_env
        ;;
    *)
        echo "❌ Unknown service: $SERVICE"
        echo "Use: api, manager, or all"
        exit 1
        ;;
esac

echo ""
echo "========================================"
echo "🎉 Environment sync complete!"
echo "========================================"

