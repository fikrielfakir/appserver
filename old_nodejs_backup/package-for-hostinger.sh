#!/bin/bash

# Package Application for Hostinger Deployment
# This script creates a deployment-ready package

echo "🚀 Packaging Application for Hostinger..."

# Create deployment directory
DEPLOY_DIR="hostinger-package"
rm -rf $DEPLOY_DIR
mkdir -p $DEPLOY_DIR

# Build the application
echo "📦 Building application..."
npm run build

# Copy necessary files
echo "📋 Copying files..."

# Copy built files
cp -r dist $DEPLOY_DIR/
cp -r node_modules $DEPLOY_DIR/

# Copy configuration files
cp package.json $DEPLOY_DIR/
cp package-lock.json $DEPLOY_DIR/
cp ecosystem.config.js $DEPLOY_DIR/
cp .htaccess $DEPLOY_DIR/

# Copy environment example (user needs to edit this)
cp .env.example $DEPLOY_DIR/.env

# Copy database SQL file
cp database_mysql.sql $DEPLOY_DIR/

# Copy documentation
cp PACKAGE_README.md $DEPLOY_DIR/README.txt
cp HOSTINGER_DEPLOYMENT.md $DEPLOY_DIR/
cp DEPLOYMENT_CHECKLIST.md $DEPLOY_DIR/
cp DEPLOYMENT_SUMMARY.md $DEPLOY_DIR/
cp ANDROID_INTEGRATION.md $DEPLOY_DIR/
cp INTEGRATION_REVIEW.md $DEPLOY_DIR/

# Create logs directory
mkdir -p $DEPLOY_DIR/logs

# Create a README for deployment
cat > $DEPLOY_DIR/DEPLOY_INSTRUCTIONS.txt << 'EOF'
=================================================================
DEPLOYMENT PACKAGE FOR HOSTINGER
=================================================================

This package contains everything needed to deploy your Android
Platform Control Dashboard to Hostinger.

QUICK START:
1. Extract this package
2. Edit .env file with your database credentials
3. Upload entire folder to your Hostinger hosting
4. Import database_mysql.sql into your MySQL database
5. Configure Node.js application in Hostinger hPanel
6. Start the application

DETAILED INSTRUCTIONS:
See README.md (HOSTINGER_DEPLOYMENT.md) for complete step-by-step guide.

IMPORTANT FILES:
- database_mysql.sql: Database schema to import
- .env: Configuration file (EDIT THIS!)
- dist/: Built application files
- ecosystem.config.js: PM2 configuration
- .htaccess: Apache proxy configuration

DEFAULT LOGIN:
- Username: admin
- Password: admin123
- ⚠️ CHANGE THIS IMMEDIATELY AFTER FIRST LOGIN!

SUPPORT:
- Android Integration: See ANDROID_INTEGRATION.md
- Integration Review: See INTEGRATION_REVIEW.md
- Deployment Guide: See README.md

=================================================================
EOF

# Create ZIP archive
echo "📦 Creating ZIP archive..."
cd $DEPLOY_DIR
zip -r ../hostinger-deployment-package.zip ./*
cd ..

echo "✅ Package created successfully!"
echo ""
echo "📁 Package location: ./hostinger-deployment-package.zip"
echo "📁 Unzipped files: ./$DEPLOY_DIR/"
echo ""
echo "🔍 Package contents:"
echo "  - dist/ (built application)"
echo "  - node_modules/ (dependencies)"
echo "  - database_mysql.sql (database schema)"
echo "  - .env (configuration - EDIT THIS!)"
echo "  - ecosystem.config.js (PM2 config)"
echo "  - .htaccess (Apache config)"
echo "  - Documentation files"
echo ""
echo "📖 Next steps:"
echo "  1. Extract hostinger-deployment-package.zip"
echo "  2. Edit .env with your database credentials"
echo "  3. Follow instructions in README.md"
echo ""
echo "🎉 Ready for deployment!"
