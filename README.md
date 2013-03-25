Access Control Server
=====================

A RESTful server that works with the Access Control Nodes (https://github.com/solexious/ACNode)
Protocol loosely based on http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal

STATUS: Working, but very partial functionality and documentation

- [X] One way sync from membership json file
- [ ] Two way sync with membership json file
- [ ] GET /[nodeID]/card/
- [ ] POST /[nodeID]/card/
- [ ] GET /[nodeID]/sync/
- [ ] GET /[nodeID/sync/[last received card]/
- [ ] PUT /[nodeID]/status/
- [ ] GET /[nodeID]/status/
- [ ] PUT /[nodeID]/tooluse/
- [ ] POST /[nodeID]/tooluse/time/
- [ ] PUT /[nodeID]/case/



Usage:
curl http://[server]:[port]/[node_id]/card/[card_id]

For testing it's installed on babbage port 1234