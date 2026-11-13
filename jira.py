import requests
from requests.auth import HTTPBasicAuth
import json

# Configuration
DOMAIN = "isbteam.atlassian.net"
EMAIL = "iabdulazizli@isb.az"
API_TOKEN = "ATATT3xFfGF0nttAqoiuvXBy8AA35fhgDj4z9fg08Z3m0HtkqZZCzeCtPHbUg7PHWwFq4LaNwSN-77YPuNPiHyXvgDf5DFJtWIpkohOjRmGXamgDIOUvJ3VJ9CplaNT8cZSUgV4fEVM3eG8APwklh9fuy_BFO6UJpfULQnujfl3-imgW6FE3yxs=8F98C663"



# Define search constraints
PROJECT_KEY = "IIS"
jql_query = f'project = "{PROJECT_KEY}" AND status = "To Do"'


url = f"https://{DOMAIN}/rest/api/3/search/jql"
auth = HTTPBasicAuth(EMAIL, API_TOKEN)

headers = {
   "Accept": "application/json",
   "Content-Type": "application/json"
}

# 2. Move constraints from query parameters into a JSON request body

payload = {
    "jql": "order by created DESC",  # Fetches the newest tickets across all your projects
    "maxResults": 5,
    "fields": ["summary", "status"]
}


# 3. Change the HTTP method from requests.get to requests.post
response = requests.post(url, headers=headers, auth=auth, json=payload)

if response.status_code == 200:
    data = response.json()
    issues = data.get("issues", [])
    
    if not issues:
        print("Success! Connected to Jira, but zero issues matched your JQL query.")
        print(f"Raw Response: {data}")  # Inspect total issues available or metadata
    else:
        for issue in issues:
            print(f"Key: {issue['key']}")
            print(f"Summary: {issue['fields']['summary']}")
            print(f"Status: {issue['fields']['status']['name']}\n")
else:
    print(f"Failed to fetch issues. Status code: {response.status_code}")
    print(response.text)

