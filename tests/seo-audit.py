"""On-page SEO audit (content-optimizer checklist) against the local server."""
import re, sys, urllib.request
from html.parser import HTMLParser
BASE = sys.argv[1] if len(sys.argv) > 1 else 'http://127.0.0.1:8080'
PAGES = {'/': 'orthopedic', '/product/mars-pillow': 'contour memory foam pillow', '/faq': 'orthopedic pillow', '/about': 'neck pillow', '/contact': 'contact',
         '/shipping-policy': 'shipping', '/returns': 'return', '/privacy-policy': 'privacy', '/terms-of-service': 'terms', '/accessibility': 'accessibility'}
class P(HTMLParser):
    def __init__(s): super().__init__(); s.h=[]; s.cur=None; s.text=[]; s.links=[]; s.skip=0; s.title=''; s.desc=''; s.intitle=False; s.inmain=False
    def handle_starttag(s,t,a):
        a=dict(a)
        if t in('script','style','svg'): s.skip+=1
        if t=='main': s.inmain=True
        if t=='title': s.intitle=True
        if t=='meta' and a.get('name')=='description': s.desc=a.get('content','')
        if re.fullmatch(r'h[1-6]',t): s.cur=[int(t[1]),'']; s.h.append(s.cur)
        if t=='a' and s.inmain and a.get('href'): s.links.append(a['href'])
    def handle_endtag(s,t):
        if t in('script','style','svg'): s.skip-=1
        if t=='title': s.intitle=False
        if re.fullmatch(r'h[1-6]',t): s.cur=None
        if t=='main': s.inmain=False
    def handle_data(s,d):
        if s.intitle: s.title+=d
        if s.skip: return
        if s.cur is not None: s.cur[1]+=d
        if s.inmain: s.text.append(d)
bad=0
for path,kw in PAGES.items():
    html=urllib.request.urlopen(BASE+path).read().decode()
    p=P(); p.feed(html)
    words=re.findall(r"[A-Za-z0-9'’-]+",' '.join(p.text)); text=' '.join(words).lower()
    h1=[h for h in p.h if h[0]==1]
    levels=[h[0] for h in p.h]; skipped=any(b-a>1 for a,b in zip(levels,levels[1:]))
    internal=[l for l in p.links if l.startswith('/') or l.startswith('#')]; external=[l for l in p.links if l.startswith('http')]
    first100=' '.join(words[:100]).lower()
    h2kw=any(kw.split()[0] in h[1].lower() for h in p.h if h[0]==2)
    checks={
      'title 50-60': 50<=len(p.title)<=60, 'desc 150-160': 150<=len(p.desc)<=160, 'one H1': len(h1)==1,
      'kw in H1': bool(h1) and kw.split()[0] in h1[0][1].lower(), 'kw first 100w': kw.split()[0] in first100,
      'no skipped levels': not skipped, '3+ internal links': len(internal)>=3, 'kw in an H2': h2kw,
    }
    fails=[k for k,v in checks.items() if not v]
    bad+=bool(fails)
    print(f"{path:22} words={len(words):4} title={len(p.title)} desc={len(p.desc)} h1={len(h1)} int={len(internal)} ext={len(external)}  {'OK' if not fails else 'FIX: '+', '.join(fails)}")
sys.exit(1 if bad else 0)
