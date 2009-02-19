        <div id="header">
          <h1>_{Welcome aboard}</h1>
          <h2>_{You&rsquo;re using Editam!}</h2>
        </div>

        <div id="main-content">
          <h1>_{You are about to install Editam}</h1>
          <p>
            _{Editam is a minimalistic Content Management System.}
          </p>
          <p>_{Use this web installer to setup your Editam in just few steps:}</p>
          <ol>
            <li>
              <h2>_{Configure your environment}</h2>
              <p><?=$text_helper->translate('<a href="%url">Run a step by step wizard for creating a configuration file</a> or read README.txt instead.',array('%url'=>$url_helper->url_for(array('action'=>'select_database'))))?></p>
            </li>
            
          </ol>
        </div>
        <div id="next-step">
            <p>
                <a href="<?=$url_helper->url_for(array('action'=>'select_database'))?>">_{Start the configuration wizard}</a>
            </p>
        </div>