#!/bin/bash

# Script untuk generate views berdasarkan template users
# Usage: ./generate-entity-views.sh <entity_name> [entity_display_name]
# Example: ./generate-entity-views.sh categories "Category"

if [ $# -lt 1 ]; then
    echo "Usage: $0 <entity_name> [entity_display_name]"
    echo "Example: $0 categories 'Category'"
    exit 1
fi

ENTITY_NAME=$1
ENTITY_DISPLAY_NAME=${2:-$(echo $ENTITY_NAME | sed 's/.*/\u&/' | sed 's/s$//')}
ENTITY_SINGULAR=$(echo $ENTITY_NAME | sed 's/s$//')
ENTITY_SINGULAR_LOWER=$(echo $ENTITY_SINGULAR | tr '[:upper:]' '[:lower:]')
ENTITY_PLURAL_LOWER=$(echo $ENTITY_NAME | tr '[:upper:]' '[:lower:]')

# Base paths
BASE_PATH="/Users/macbookprom12020/Documents/coding/laravel/laravel-management-assets"
USERS_VIEWS_PATH="$BASE_PATH/resources/views/dashboard/users"
USERS_COMPONENTS_PATH="$BASE_PATH/resources/views/components/users"
TARGET_VIEWS_PATH="$BASE_PATH/resources/views/dashboard/$ENTITY_PLURAL_LOWER"
TARGET_COMPONENTS_PATH="$BASE_PATH/resources/views/components/$ENTITY_PLURAL_LOWER"

echo "Generating views for entity: $ENTITY_NAME"
echo "Display name: $ENTITY_DISPLAY_NAME"
echo "Singular: $ENTITY_SINGULAR"
echo "Plural: $ENTITY_PLURAL_LOWER"
echo ""

# Create target directories
echo "Creating directories..."
mkdir -p "$TARGET_VIEWS_PATH"
mkdir -p "$TARGET_COMPONENTS_PATH"

# Function to replace content in files
replace_content() {
    local file=$1
    
    # Replace various forms of 'user' with entity equivalents
    sed -i '' "s/users/$ENTITY_PLURAL_LOWER/g" "$file"
    sed -i '' "s/Users/$ENTITY_NAME/g" "$file"
    sed -i '' "s/User/$ENTITY_DISPLAY_NAME/g" "$file"
    sed -i '' "s/user/$ENTITY_SINGULAR_LOWER/g" "$file"
    
    # Replace route names
    sed -i '' "s/route('users\./route('$ENTITY_PLURAL_LOWER./g" "$file"
    
    # Replace model references
    sed -i '' "s/\$user/\$$ENTITY_SINGULAR_LOWER/g" "$file"
    sed -i '' "s/@user/@$ENTITY_SINGULAR_LOWER/g" "$file"
    
    # Replace specific user fields with generic ones (you may need to customize this)
    sed -i '' "s/name/name/g" "$file"
    sed -i '' "s/email/email/g" "$file"
    
    echo "  ✓ Processed: $file"
}

# Copy and process dashboard views
echo "Copying dashboard views..."
for file in "$USERS_VIEWS_PATH"/*.blade.php; do
    if [ -f "$file" ]; then
        filename=$(basename "$file")
        target_file="$TARGET_VIEWS_PATH/$filename"
        cp "$file" "$target_file"
        replace_content "$target_file"
    fi
done

# Copy and process component views
echo "Copying component views..."
for file in "$USERS_COMPONENTS_PATH"/*.blade.php; do
    if [ -f "$file" ]; then
        filename=$(basename "$file")
        target_file="$TARGET_COMPONENTS_PATH/$filename"
        cp "$file" "$target_file"
        replace_content "$target_file"
    fi
done

echo ""
echo "✅ Successfully generated views for $ENTITY_NAME!"
echo "📁 Dashboard views: $TARGET_VIEWS_PATH"
echo "📁 Component views: $TARGET_COMPONENTS_PATH"
echo ""
echo "⚠️  Note: You may need to manually adjust:"
echo "   - Field names specific to your entity"
echo "   - Validation rules"
echo "   - Display labels"
echo "   - Table columns"
echo "   - Form fields"
echo ""
echo "💡 Usage examples:"
echo "   ./generate-entity-views.sh categories 'Category'"
echo "   ./generate-entity-views.sh locations 'Location'"
echo "   ./generate-entity-views.sh products 'Product'"