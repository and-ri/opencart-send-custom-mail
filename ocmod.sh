name="sendmail"
version="1.0.$(date +%Y%m%d%H%M%S)"

# create dist directory if not exist
mkdir -p ./dist

# remove old files if exist
rm -f ./dist/$name-$version.ocmod.zip

# create new zip file
zip -r ./dist/$name-$version.ocmod.zip ./upload ./install.xml