var xcode = require("xcode");
var fs = require("fs");
var path = require("path");

//Path to the project.pbxproj file
const projectPath = "platforms/ios/Bruveg.xcodeproj/project.pbxproj";
const myProj = xcode.project(projectPath);

TARGET_BUILD_DIR = "${TARGET_BUILD_DIR}";
WRAPPER_NAME = "${WRAPPER_NAME}";
ARCHS = "${ARCHS}";
EXTRACTED_ARCHS = "${EXTRACTED_ARCHS[@]}";

var options = {
  shellPath: "/bin/sh",
  // Here you can add your own shellScript
  shellScript: `APP_PATH="${TARGET_BUILD_DIR}/${WRAPPER_NAME}"

    # This script loops through the frameworks embedded in the application and
    # removes unused architectures.
    find "$APP_PATH" -name '*.framework' -type d | while read -r FRAMEWORK
    do
    FRAMEWORK_EXECUTABLE_NAME=$(defaults read "$FRAMEWORK/Info.plist" CFBundleExecutable)
    FRAMEWORK_EXECUTABLE_PATH="$FRAMEWORK/$FRAMEWORK_EXECUTABLE_NAME"
    echo "Executable is $FRAMEWORK_EXECUTABLE_PATH"

    EXTRACTED_ARCHS=()

    for ARCH in $ARCHS
    do
    echo "Extracting $ARCH from $FRAMEWORK_EXECUTABLE_NAME"
    lipo -extract "$ARCH" "$FRAMEWORK_EXECUTABLE_PATH" -o "$FRAMEWORK_EXECUTABLE_PATH-$ARCH"
    EXTRACTED_ARCHS+=("$FRAMEWORK_EXECUTABLE_PATH-$ARCH")
    done

    echo "Merging extracted architectures: ${ARCHS}"
    lipo -o "$FRAMEWORK_EXECUTABLE_PATH-merged" -create "${EXTRACTED_ARCHS}"
    rm "${EXTRACTED_ARCHS}"

    echo "Replacing original executable with thinned version"
    rm "$FRAMEWORK_EXECUTABLE_PATH"
    mv "$FRAMEWORK_EXECUTABLE_PATH-merged" "$FRAMEWORK_EXECUTABLE_PATH"

    done`
};

myProj.parse(function(err) {
  myProj.addBuildPhase(
    [],
    "PBXShellScriptBuildPhase",
    "Run script",
    myProj.getFirstTarget().uuid,
    options
  );
  fs.writeFileSync(projectPath, myProj.writeSync());
});
