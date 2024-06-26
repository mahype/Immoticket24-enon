#!/bin/bash

# Check if WP-CLI is installed
if ! command -v wp &> /dev/null
then
    echo "WP-CLI could not be found. Please install it first."
    exit
fi

API_KEY="60P5IsAxV3xGBxaa3aamCY5WKdZWi9jhB3Xe5HY5xRE"

# Navigate to the WordPress installation directory
# Replace /path/to/wordpress with the actual path

# Update the list of plugins
wp plugin list --format=json > plugins.json

# Read the plugin list
plugins=$(jq -c '.[]' plugins.json)

# Loop through each plugin
for plugin in $plugins
do
    # Extract plugin details
    slug=$(echo $plugin | jq -r '.name')
    name=$(echo $plugin | jq -r '.name')
    version=$(echo $plugin | jq -r '.version')
    update=$(echo $plugin | jq -r '.update_version')

    echo "Checking plugin: $name (Version: $version)"

    # Check if an update is available
    if [ "$update" != "null" ] && [ "$update" != "" ]; then
        echo "Update available for $name. Checking for security vulnerabilities..."

        # Check for vulnerabilities (replace <API_KEY> with your actual API key if needed)
        response=$(curl -s -H "Authorization: Token token=$API_KEY" "https://wpvulndb.com/api/v3/plugins/$slug")
        
        # Check if the response contains vulnerabilities
        vulnerabilities=$(echo $response | jq -r '.[].vulnerabilities | select(. != null)')

        if [ -n "$vulnerabilities" ]; then
            echo "Security vulnerabilities found for $name:"
            echo "$vulnerabilities" | jq -r '.[] | .title'
        else
            echo "No security vulnerabilities found for $name in version $update."
        fi
    else
        echo "No update available for $name."
    fi

    echo "---------------------------------"
done

# Clean up
rm plugins.json
