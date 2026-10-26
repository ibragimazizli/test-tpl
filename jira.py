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

url = f"https://{DOMAIN}/rest/api/3/search"
auth = HTTPBasicAuth(EMAIL, API_TOKEN)

headers = {
   "Accept": "application/json",
   "Content-Type": "application/json"
}

query_params = {
    "jql": jql_query,
    "maxResults": 10,
    "fields": "summary,status"  # Minimizes JSON payload size
}

response = requests.get(url, headers=headers, auth=auth, params=query_params)

if response.status_code == 200:
    data = response.json()
    # Iterate through the extracted list of issues
    for issue in data.get("issues", []):
        print(f"Key: {issue['key']}")
        print(f"Summary: {issue['fields']['summary']}")
        print(f"Status: {issue['fields']['status']['name']}\n")
else:
    print(f"Failed to fetch issues. Status code: {response.status_code}")
    print(response.text)
