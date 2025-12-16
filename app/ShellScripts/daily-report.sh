#!/bin/bash
#メッセージを保存する一時ファイル
MESSAGEFILE=$(mktemp -t webhooksdf.XXXXXXXXXX)
MESSAGE=`df ${MESSAGEFILE}`
# URL="https://samplefc.local/api/contact/slack"
URL="http://localhost/api/contact/slack"

response=$(curl -sS -w "%{http_code}" $URL)

http_code=$(tail -n1 <<< "$response")  # get the last line
content=$(sed '$ d' <<< "$response")   # get all but the last line which contains the status code

echo "$response"
echo "$content"

# Slack webhook URL removed for security - use environment variable if needed
# curl -X POST -H 'Content-type: application/json' --data "{'text': 'aaaa' }" $SLACK_WEBHOOK_URL

#一時ファイルの削除
rm ${MESSAGEFILE}
if [ -p /dev/stdin ] ; then
    #改行コードをslack用に変換
   cat - | tr '\n' '\\' | sed 's/\\/\\n/g' > ${MESSAGEFILE}
else
    echo "nothing stdin"
    exit 1
fi
