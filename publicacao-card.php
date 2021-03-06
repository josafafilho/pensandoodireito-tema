<!-- inicio card -->
<li data-id="<?php echo get_the_ID(); ?>" class="publicacoes-item">
    <div class="row">
        <div class="col-sm-8 col-xs-12">
            <div class="descricao">
                <p>

                <h3 class="h5 red font-roboto">
                    <strong>Volume <?php echo get_post_meta(get_the_ID(), 'pub_number', true); ?></strong></h3></p>
                <h4 class="red h3">
                    <strong>
                        <?php the_title(); ?>
                    </strong>
                </h4>

                <p class="mt-md">
                    <a data-toggle="collapse" href="#resumo-<?php echo get_the_ID(); ?>" aria-expanded="false"
                       aria-controls="resumo-<?php echo get_the_ID(); ?>"> Resumo <i class="fa fa-caret-down"></i></a>
                </p>
                <?php
                $lista_autores = get_autores_from_excerpt(get_the_excerpt());
                ?>
                <div class="collapse" id="resumo-<?php echo get_the_ID(); ?>"><?php the_content(); ?></div>

                <p>
                    <small class="text-muted">
                        Publicado em: <?php echo get_post_meta(get_the_ID(), 'pub_date', true); ?><br/>
                        <?php if (!empty($lista_autores)): ?> Coordenação: <?php echo implode(' e ', $lista_autores); ?><?php endif; ?>
                    </small>
                </p>
            </div>
        </div>
        <div class="col-sm-4 col-xs-12 mt-lg">
            <div class="panel panel-submenu text-center">
                <div class="panel-body">
                    <p><a href="<?php echo get_post_meta(get_the_ID(), 'pub_dld_file', true); ?>"
                          class="btn btn-danger"><i
                                class="fa fa-download"></i> Download volume <?php echo get_post_meta(get_the_ID(), 'pub_number', true); ?></a></p>

                    <ul class="list-inline social-icons text-muted divider-top">
                        <li class="social-icons-rounded">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo get_post_permalink(); ?>"
                               target="_blank"
                               class="btn btn-rounded text-muted"
                               data-toggle="tooltip"
                               data-placement="top"
                               title="Compartilhe no Facebook"><i
                                    class="fa fa-facebook"></i></a>
                        </li>
                        <li class="social-icons-rounded">
                            <a href="https://twitter.com/share?hashtags=pensandoodireito&text=<?php echo the_title(); ?>&url=<?php echo get_post_permalink(); ?>"
                               target="_blank"
                               class="btn btn-rounded text-muted"
                               data-toggle="tooltip"
                               data-placement="top"
                               title="Compartilhe no Twitter"><i
                                    class="fa fa-twitter"></i></a>
                        </li>
                        <li class="social-icons-rounded">
                            <a
                                href="https://www.linkedin.com/shareArticle?mini=true&title=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_post_permalink()); ?>&summary=<?php echo urlencode(get_the_excerpt()); ?>&source=http://pensando.mj.gov.br"
                                target="_blank" class="btn btn-rounded text-muted"
                                data-toggle="tooltip"
                                data-placement="top" title="Compartilhe no Linkedin"><i
                                    class="fa fa-linkedin"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</li>
<!-- fim card -->
