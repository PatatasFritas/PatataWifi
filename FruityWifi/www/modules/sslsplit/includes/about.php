SSLsplit - transparent and scalable SSL/TLS interception.
<br><br>
Author: Daniel Roethlisberger


<h2>Overview</h2>
<p>SSLsplit is a tool for man-in-the-middle attacks against SSL/TLS encrypted network connections. Connections are transparently intercepted through a network address translation engine and redirected to SSLsplit. SSLsplit terminates SSL/TLS and initiates a new SSL/TLS connection to the original destination address, while logging all data transmitted. SSLsplit is intended to be useful for network forensics and penetration testing.</p>


<p>SSLsplit supports plain TCP, plain SSL, HTTP and HTTPS connections over both IPv4 and IPv6. For SSL and HTTPS connections, SSLsplit generates and signs forged X509v3 certificates on-the-fly, based on the original server certificate subject DN and subjectAltName extension. SSLsplit fully supports Server Name Indication (SNI) and is able to work with RSA, DSA and ECDSA keys and DHE and ECDHE cipher suites. Depending on the version of OpenSSL, SSLsplit supports SSL 3.0, TLS 1.0, TLS 1.1 and TLS 1.2, and optionally SSL 2.0 as well. SSLsplit can also use existing certificates of which the private key is available, instead of generating forged ones. SSLsplit supports NULL-prefix CN certificates and can deny OCSP requests in a generic way. For HTTP and HTTPS connections, SSLsplit removes response headers for HPKP in order to prevent public key pinning, for HSTS to allow the user to accept untrusted certificates, and Alternate Protocols to prevent switching to QUIC/SPDY.</p>

<style>
    .keyword,
.id,
.phpdoc,
.title,
.vbscript .built_in,
.rsl .built_in,
.cpp .built_in,
.aggregate,
.smalltalk .class,
.winutils,
.bash .variable {
  font-weight: bold;
}

.string,
.title,
.parent,
.tag .attribute .value,
.rules .value,
.rules .value .number,
.preprocessor,
.ruby .symbol,
.instancevar,
.aggregate,
.template_tag,
.django .variable,
.smalltalk .class,
.addition,
.flow,
.stream,
.bash .variable {
  color: #800;
}

.number,
.regexp,
.literal,
.smalltalk .symbol,
.smalltalk .char,
.change {
  color: #080;
}
</style>

<pre><code class=" sql">% sslsplit -h
<span class="keyword">Usage</span>: sslsplit [options...] [proxyspecs...]
  -c pemfile  use CA cert (<span class="keyword">and</span> <span class="keyword">key</span>) <span class="keyword">from</span> pemfile <span class="keyword">to</span> sign forged certs
  -k pemfile  use CA <span class="keyword">key</span> (<span class="keyword">and</span> cert) <span class="keyword">from</span> pemfile <span class="keyword">to</span> sign forged certs
  -C pemfile  use CA chain <span class="keyword">from</span> pemfile (intermediate <span class="keyword">and</span> root CA certs)
  -K pemfile  use <span class="keyword">key</span> <span class="keyword">from</span> pemfile <span class="keyword">for</span> leaf certs (<span class="keyword">default</span>: generate)
  -t certdir  use cert+chain+<span class="keyword">key</span> PEM files <span class="keyword">from</span> certdir <span class="keyword">to</span> target <span class="keyword">all</span> sites
              matching the common <span class="keyword">names</span> (non-matching: generate if CA)
  -O          deny <span class="keyword">all</span> OCSP requests <span class="keyword">on</span> <span class="keyword">all</span> proxyspecs
  -P          passthrough SSL connections if they cannot be split because <span class="keyword">of</span>
              client cert auth <span class="keyword">or</span> <span class="keyword">no</span> matching cert <span class="keyword">and</span> <span class="keyword">no</span> CA (<span class="keyword">default</span>: <span class="keyword">drop</span>)
  -g pemfile  use DH <span class="keyword">group</span> params <span class="keyword">from</span> pemfile (<span class="keyword">default</span>: keyfiles <span class="keyword">or</span> auto)
  -G curve    use ECDH named curve (<span class="keyword">default</span>: secp160r2 <span class="keyword">for</span> non-RSA leafkey)
  -Z          disable SSL/TLS compression <span class="keyword">on</span> <span class="keyword">all</span> connections
  -r proto    <span class="keyword">only</span> support one <span class="keyword">of</span> ssl3 tls10 tls11 tls12 (<span class="keyword">default</span>: <span class="keyword">all</span>)
  -R proto    disable one <span class="keyword">of</span> ssl3 tls10 tls11 tls12 (<span class="keyword">default</span>: none)
  -s ciphers  use the given OpenSSL cipher suite spec (<span class="keyword">default</span>: <span class="keyword">ALL</span>:-aNULL)
  -e engine   specify <span class="keyword">default</span> NAT engine <span class="keyword">to</span> use (<span class="keyword">default</span>: pf)
  -E          list available NAT engines <span class="keyword">and</span> exit
  -u <span class="keyword">user</span>     <span class="keyword">drop</span> <span class="keyword">privileges</span> <span class="keyword">to</span> <span class="keyword">user</span> (<span class="keyword">default</span> if run <span class="keyword">as</span> root: nobody)
  -m <span class="keyword">group</span>    <span class="keyword">when</span> <span class="keyword">using</span> -u, override <span class="keyword">group</span> (<span class="keyword">default</span>: <span class="keyword">primary</span> <span class="keyword">group</span> <span class="keyword">of</span> <span class="keyword">user</span>)
  -j jaildir  chroot() <span class="keyword">to</span> jaildir (impacts -S/-F <span class="keyword">and</span> sni, see manual page)
  -p pidfile  <span class="keyword">write</span> pid <span class="keyword">to</span> pidfile (<span class="keyword">default</span>: <span class="keyword">no</span> pid file)
  -l logfile  <span class="keyword">connect</span> log: log one line summary per <span class="keyword">connection</span> <span class="keyword">to</span> logfile
  -L logfile  content log: <span class="keyword">full</span> data <span class="keyword">to</span> file <span class="keyword">or</span> named pipe (excludes -S/-F)
  -S logdir   content log: <span class="keyword">full</span> data <span class="keyword">to</span> separate files <span class="keyword">in</span> dir (excludes -L/-F)
  -F pathspec content log: <span class="keyword">full</span> data <span class="keyword">to</span> sep files <span class="keyword">with</span> % subst (excl. -L/-S):
              %T - initial <span class="keyword">connection</span> <span class="keyword">time</span> <span class="keyword">as</span> an ISO <span class="number">8601</span> UTC <span class="keyword">timestamp</span>
              %d - dest address:port
              %s - source address:port
              %x - base name <span class="keyword">of</span> <span class="keyword">local</span> process        (requires -i)
              %X - <span class="keyword">full</span> path <span class="keyword">to</span> <span class="keyword">local</span> process        (requires -i)
              %u - <span class="keyword">user</span> name <span class="keyword">or</span> id <span class="keyword">of</span> <span class="keyword">local</span> process  (requires -i)
              %g - <span class="keyword">group</span> name <span class="keyword">or</span> id <span class="keyword">of</span> <span class="keyword">local</span> process (requires -i)
              %% - literal <span class="string">'%'</span>
      e.g.    <span class="string">"/var/log/sslsplit/%X/%u-%s-%d-%T.log"</span>
  -i          look up <span class="keyword">local</span> process owning each <span class="keyword">connection</span> <span class="keyword">for</span> logging
  -d          daemon mode: run <span class="keyword">in</span> background, log error messages <span class="keyword">to</span> syslog
  -D          debug mode: run <span class="keyword">in</span> foreground, log debug messages <span class="keyword">on</span> stderr
  -V          print version information <span class="keyword">and</span> exit
  -h          print <span class="keyword">usage</span> information <span class="keyword">and</span> exit
  proxyspec = type listenaddr+port [natengine|targetaddr+port|<span class="string">"sni"</span>+port]
      e.g.    http <span class="number">0.0</span>.<span class="number">0.0</span> <span class="number">8080</span> www.roe.ch <span class="number">80</span>  # http/<span class="number">4</span>; static hostname dst
              https ::<span class="number">1</span> <span class="number">8443</span> <span class="number">2001</span>:db8::<span class="number">1</span> <span class="number">443</span>   # https/<span class="number">6</span>; static address dst
              https <span class="number">127.0</span>.<span class="number">0.1</span> <span class="number">9443</span> sni <span class="number">443</span>     # https/<span class="number">4</span>; SNI DNS lookups
              tcp <span class="number">127.0</span>.<span class="number">0.1</span> <span class="number">10025</span>              # tcp/<span class="number">4</span>; <span class="keyword">default</span> NAT engine
              ssl <span class="number">2001</span>:db8::<span class="number">2</span> <span class="number">9999</span> pf          # ssl/<span class="number">6</span>; NAT engine <span class="string">'pf'</span>
Example:
  sslsplit -k ca.<span class="keyword">key</span> -c ca.pem -P  https <span class="number">127.0</span>.<span class="number">0.1</span> <span class="number">8443</span>  https ::<span class="number">1</span> <span class="number">8443</span>
</code></pre>
