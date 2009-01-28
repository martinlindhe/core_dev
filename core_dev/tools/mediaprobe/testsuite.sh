#!/bin/bash

make all

img_formats=(
	"media/image_bmp_WinV3.bmp"        "image/bmp"
	"media/image_bmp_WinV4.bmp"        "image/bmp"
	"media/image_bmp_WinV5.bmp"        "image/bmp"
	"media/image_bmp_OS2V1.bmp"        "image/bmp"
	"media/image_bmp_OS2V2.bmp"        "image/bmp"
	"media/image_gif_87a.gif"          "image/gif"
	"media/image_gif_89a.gif"          "image/gif"
	"media/image_gif_89a_animated.gif" "image/gif"
	"media/image_jpeg.jpg"             "image/jpeg"
	"media/image_mng.mng"              "image/x-mng"
	"media/image_png.png"              "image/png"
)

aud_formats=(
	"media/audio_wma.wma"              "audio/x-ms-wma"
	"media/audio_mp3.mp3"              "audio/mpeg"
)

vid_formats=(
	"media/video_wmv.wmv"              "video/x-ms-wmv"
)

testRun() {
	local x=( "$@" )

	for ((i=0; i<${#x[@]}; i+=2)); do
    	RUN=`./mediaprobe ${x[${i}]}`
		if [ $RUN != ${x[${i}+1]} ]; then
			echo "FAIL ${x[${i}]}: $RUN"
		else
			echo "OK ${x[${i}]}"
		fi
	done
}


echo "Running image tests ..."
testRun "${img_formats[@]}"


echo "Running audio tests ..."
testRun "${aud_formats[@]}"


echo "Running video tests ..."
testRun "${vid_formats[@]}"
